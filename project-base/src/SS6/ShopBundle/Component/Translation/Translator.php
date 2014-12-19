<?php

namespace SS6\ShopBundle\Component\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\MessageSelector;

class Translator extends BaseTranslator {

	const DEFAULT_DOMAIN = 'messages';
	const NOT_TRANSLATED_PREFIX = '##';
	const TRANSLATION_ID_LOCALE = 'cs';

	/**
	 * @var \Symfony\Component\Translation\MessageSelector
	 */
	private $messageSelector;

	public function __construct(
		ContainerInterface $container,
		MessageSelector $selector,
		$loaderIds = array(),
		array $options = array()
	) {
		parent::__construct($container, $selector, $loaderIds, $options);

		$this->messageSelector = $selector ?: new MessageSelector();
	}

	/**
	 * @param string $locale
	 * @return \Symfony\Component\Translation\MessageCatalogue
	 */
	public function getCatalogue($locale) {
		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		return $this->catalogues[$locale];
	}

	/**
	 * When translation for given locale is not defined and locale is not self::TRANSLATION_ID_LOCALE,
	 * function returns translation id string with self::NOT_TRANSLATED_PREFIX prefix
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function trans($id, array $parameters = array(), $domain = self::DEFAULT_DOMAIN, $locale = null) {
		if ($locale === null) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$catalogue = $this->catalogues[$locale];
		if ($catalogue->defines($id, $domain)) {
			return strtr($this->catalogues[$locale]->get((string)$id, $domain), $parameters);
		} elseif ($locale === self::TRANSLATION_ID_LOCALE) {
			return $id;
		} else {
			return self::NOT_TRANSLATED_PREFIX . $id;
		}
	}

	/**
	 * When translation for given locale is not defined locale is not self::TRANSLATION_ID_LOCALE,
	 * function returns translation id string with self::NOT_TRANSLATED_PREFIX prefix
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function transChoice($id, $number, array $parameters = array(), $domain = self::DEFAULT_DOMAIN, $locale = null) {
		if ($locale === null) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$id = (string)$id;

		$catalogue = $this->catalogues[$locale];
		while (!$catalogue->defines($id, $domain)) {
			if ($catalogue->getFallbackCatalogue()) {
				$cat = $catalogue->getFallbackCatalogue();
				$catalogue = $cat;
				$locale = $catalogue->getLocale();
			} else {
				break;
			}
		}

		if ($catalogue->defines($id, $domain)) {
			return strtr($this->messageSelector->choose($catalogue->get($id, $domain), (int)$number, $locale), $parameters);
		} elseif ($locale === self::TRANSLATION_ID_LOCALE) {
			return $id;
		} else {
			return self::NOT_TRANSLATED_PREFIX . $id;
		}
	}

}
