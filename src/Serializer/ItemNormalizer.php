<?php

declare(strict_types=1);

namespace App\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * This class overrides api-platform's built in ItemNormalizer in order to make it possible to POST resources
 * with custom provided ID
 *
 * Related not merged PR and discussion: https://github.com/api-platform/core/pull/2022
 */
class ItemNormalizer extends AbstractItemNormalizer
{
    private const IDENTIFIER = 'id';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory, PropertyMetadataFactoryInterface $propertyMetadataFactory, IriConverterInterface $iriConverter, ResourceClassResolverInterface $resourceClassResolver, PropertyAccessorInterface $propertyAccessor = null, NameConverterInterface $nameConverter = null, ClassMetadataFactoryInterface $classMetadataFactory = null, ItemDataProviderInterface $itemDataProvider = null, bool $allowPlainIdentifiers = false, LoggerInterface $logger = null, iterable $dataTransformers = [], ResourceMetadataFactoryInterface $resourceMetadataFactory = null)
    {
        parent::__construct($propertyNameCollectionFactory, $propertyMetadataFactory, $iriConverter, $resourceClassResolver, $propertyAccessor, $nameConverter, $classMetadataFactory, $itemDataProvider, $allowPlainIdentifiers, [], $dataTransformers, $resourceMetadataFactory);

        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param mixed $data
     * @param string $class
     * @param string $format
     * @param array $context
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $context['api_denormalize'] = true;

        if (!isset($context['resource_class'])) {
            $context['resource_class'] = $class;
        }

        $this->setObjectToPopulate($data, $context);

        return parent::denormalize($data, $class, $format, $context);
    }

    /**
     * @param string|object $classOrObject
     * @param array $context
     * @param bool $attributesAsString
     *
     * @return array|bool|string[]|\Symfony\Component\Serializer\Mapping\AttributeMetadataInterface[]
     */
    protected function getAllowedAttributes($classOrObject, array $context, $attributesAsString = false)
    {
        $allowedAttributes = parent::getAllowedAttributes(
            $classOrObject,
            $context,
            $attributesAsString
        );

        if (\array_key_exists('allowed_extra_attributes', $context)) {
            $allowedAttributes = array_merge($allowedAttributes, $context['allowed_extra_attributes']);
        }

        return $allowedAttributes;
    }

    /**
     * @param mixed $data
     * @param array $context
     */
    protected function setObjectToPopulate($data, array &$context): void
    {
        // in PUT request OBJECT_TO_POPULATE is already set by this moment
        if (!\is_array($data) || isset($context[self::OBJECT_TO_POPULATE])) {
            return;
        }

        [$identifierName, $identifierMetadata] = $this->getResourceIdentifierData($context);

        $isUpdateAllowed = (bool) ($context['api_allow_update'] ?? false);
        $hasIdentifierInRequest = \array_key_exists(self::IDENTIFIER, $data);
        $hasWritableIdentifierInRequest = $hasIdentifierInRequest && $identifierMetadata->isWritable();
        // when it is POST, update is not allowed for top level resource, but is allowed for nested resources
        $isTopLevelResourceInPostRequest = !$isUpdateAllowed
            && $context['operation_type'] === 'collection'
            && $context['collection_operation_name'] === 'post';

        // if Resource does not have an ID OR if it is writable custom id - we should not populate Entity from DB
        if (!$hasIdentifierInRequest || ($hasWritableIdentifierInRequest && $isTopLevelResourceInPostRequest)) {
            return;
        }

        if (!$isUpdateAllowed) {
            throw new InvalidArgumentException('Update is not allowed for this operation.');
        }

        try {
            $context[self::OBJECT_TO_POPULATE] = $this->iriConverter->getItemFromIri(
                (string) $data[self::IDENTIFIER],
                $context + ['fetch_data' => true]
            );
        } catch (InvalidArgumentException $e) {
            $context[self::OBJECT_TO_POPULATE] = $this->iriConverter->getItemFromIri(
                sprintf(
                    '%s/%s',
                    $this->iriConverter->getIriFromResourceClass($context['resource_class']),
                    $data[$identifierName]
                ),
                $context + ['fetch_data' => true]
            );
        }
    }

    private function getResourceIdentifierData(array $context): array
    {
        $identifierPropertyName = null;
        $identifierPropertyMetadata = null;
        $className = $context['resource_class'];

        $properties = $this->propertyNameCollectionFactory->create($className, $context);

        foreach ($properties as $propertyName) {
            $property = $this->propertyMetadataFactory->create($className, $propertyName);

            if ($property->isIdentifier()) {
                $identifierPropertyName = $propertyName;
                $identifierPropertyMetadata = $property;
                break;
            }
        }

        if ($identifierPropertyMetadata === null) {
            throw new \LogicException(
                sprintf(
                    'Resource "%s" must have an identifier. Properties: %s.',
                    $className,
                    implode(',', iterator_to_array($properties->getIterator()))
                )
            );
        }

        return [$identifierPropertyName, $identifierPropertyMetadata];
    }
}