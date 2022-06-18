<?php

declare(strict_types=1);

namespace App\Reader\Type\Catalog;

use App\Contract\LoaderInterface;
use App\Contract\ReaderInterface;
use App\Exception\Reader\InvalidInputException;
use Symfony\Component\Config\Util\XmlUtils;

class XmlReader implements ReaderInterface
{
    public const ROOT_TAG = 'catalog';
    public const CODE = 'catalog_xml';

    private LoaderInterface $loader;

    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getCode(): string
    {
        return self::CODE;
    }

    /**
     * @inheritDoc
     */
    public function read(string $inputPath): array
    {
        $data = $this->loader->load($inputPath);

        $xml = XmlUtils::parse($data);

        $rootTag = $xml->getElementsByTagName('catalog')[0] ?? null;

        if ($rootTag === null) {
            throw InvalidInputException::becauseOfMissingRootTag(self::ROOT_TAG);
        }

        $output = XmlUtils::convertDomElementToArray($rootTag);

        // @codeCoverageIgnoreStart
        if (!is_array($output)) {
            throw InvalidInputException::becauseOfInvalidDOMElementConversionResult();
        }
        // @codeCoverageIgnoreEnd

        // @phpstan-ignore-next-line
        return !empty($output)
            ? $output[key($output)]
            : [];
    }
}
