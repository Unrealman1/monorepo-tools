<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Image\Config\ImageConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ImageController extends Controller {

	const ENTITY_NAME_PAYMENT = 'payment';
	const ENTITY_NAME_PRODUCT = 'product';
	const ENTITY_NAME_SLIDER_ITEM = 'sliderItem';
	const ENTITY_NAME_TRANSPORT = 'transport';
	const SIZE_NAME_GALLERY_THUMBNAIL = 'galleryThumbnail';
	const SIZE_NAME_LIST = 'list';
	const SIZE_NAME_THUMBNAIL = 'thumbnail';

	/**
	 * @Route("/image/overview/")
	 */
	public function overviewAction() {
		$imageFacade = $this->get('ss6.shop.image.image_facade');
		/* @var $imageFacade \SS6\ShopBundle\Model\Image\ImageFacade */

		$imageEntityConfigs = $imageFacade->getAllImageEntityConfigsByClass();

		return $this->render('@SS6Shop/Admin/Content/Image/overview.html.twig', [
			'imageEntityConfigs' => $imageEntityConfigs,
			'entityNames' => $this->getEntityNamesTranslations($imageEntityConfigs),
			'usagesByEntityAndSizeName' => $this->getImageSizeUsagesTranslations($imageEntityConfigs),
			'usagesByEntityAndTypeAndSizeName' => $this->getImageSizeWithTypeUsagesTranslations($imageEntityConfigs),
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getEntityNamesTranslations(array $imageEntityConfigs) {
		$names = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig */
			$names[$imageEntityConfig->getEntityName()] = $this->getEntityNameTranslation($imageEntityConfig->getEntityName());
		}
		return $names;
	}

	/**
	 * @param string $entityName
	 * @return string
	 */
	private function getEntityNameTranslation($entityName) {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		$entityNamesTranslations = [
			self::ENTITY_NAME_PAYMENT => $translator->trans('Platba'),
			self::ENTITY_NAME_PRODUCT => $translator->trans('Produkt'),
			self::ENTITY_NAME_SLIDER_ITEM => $translator->trans('Stránka slideru'),
			self::ENTITY_NAME_TRANSPORT => $translator->trans('Doprava'),
		];

		if (array_key_exists($entityName, $entityNamesTranslations)) {
			return $entityNamesTranslations[$entityName];
		} else {
			return $entityName;
		}
	}

	/**
	 * @param @param \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getImageSizeUsagesTranslations(array $imageEntityConfigs) {
		$usages = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig */
			foreach ($imageEntityConfig->getSizeConfigs() as $imageSizeConfig) {
				$entityName = $imageEntityConfig->getEntityName();
				$sizeName = $imageSizeConfig->getName();
				if ($sizeName === null) {
					$sizeName = ImageConfig::DEFAULT_SIZE_NAME;
				}
				$usages[$entityName][$sizeName] = $this->getImageSizeUsageTranslation($entityName, $sizeName);
			}
		}

		return $usages;
	}

	/**
	 * @param string $entityName
	 * @param string $sizeName
	 * @return string
	 */
	private function getImageSizeUsageTranslation($entityName, $sizeName) {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		$imageSizeUsagesTranslations = [
			self::ENTITY_NAME_PAYMENT => [
				ImageConfig::DEFAULT_SIZE_NAME => $translator->trans(
					'Front-end: Objednávkový proces'
				),
			],
			self::ENTITY_NAME_PRODUCT => [
				ImageConfig::DEFAULT_SIZE_NAME => $translator->trans(
					'Front-end: Hlavní obrázek na detailu produktu'
				),
				self::SIZE_NAME_GALLERY_THUMBNAIL => $translator->trans(
					'Front-end: Náhledy dalších obrázků pod hlavním obrázkem na detailu produktu'
				),
				self::SIZE_NAME_LIST => $translator->trans(
					'Front-end: Výpis produktů v oddělení, výpis akčního zboží'
				),
				self::SIZE_NAME_THUMBNAIL => $translator->trans(
					'Front-end: Náhled v našeptávači pro vyhledávání, náhled v košíku během objednávkového procesu'
				),
			],
			self::ENTITY_NAME_SLIDER_ITEM => [
				ImageConfig::DEFAULT_SIZE_NAME => $translator->trans(
					'Front-end: Slider na hlavní straně'
				),
			],
			self::ENTITY_NAME_TRANSPORT => [
				ImageConfig::DEFAULT_SIZE_NAME => $translator->trans(
					'Front-end: Objednávkový proces'
				),
			],
		];

		if (array_key_exists($sizeName, $imageSizeUsagesTranslations[$entityName])) {
			return $imageSizeUsagesTranslations[$entityName][$sizeName];
		} else {
			return $translator->trans('Není zadáno pro entitu ' . $entityName . ' a rozměr ' . $sizeName);
		}
	}

	/**
	 * @param  \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig[] $imageEntityConfigs
	 * @return string[]
	 */
	private function getImageSizeWithTypeUsagesTranslations(array $imageEntityConfigs) {
		$usages = [];
		foreach ($imageEntityConfigs as $imageEntityConfig) {
			/* @var $imageEntityConfig \SS6\ShopBundle\Model\Image\Config\ImageEntityConfig */
			foreach ($imageEntityConfig->getSizeConfigsByTypes() as $typeName => $imageSizeConfigs) {
				foreach ($imageSizeConfigs as $imageSizeConfig) {
					/* @var $imageSizeConfig \SS6\ShopBundle\Model\Image\Config\ImageSizeConfig */
					$entityName = $imageEntityConfig->getEntityName();
					$sizeName = $imageSizeConfig->getName();
					if ($sizeName === null) {
						$sizeName = ImageConfig::DEFAULT_SIZE_NAME;
					}
					$usages[$entityName][$typeName][$sizeName] = $this->getImageSizeWithTypeUsageTranslation(
						$entityName,
						$typeName,
						$sizeName
					);
				}

			}
		}

		return $usages;
	}

	/**
	 * @param string $entityName
	 * @param string $typeName
	 * @param string $sizeName
	 * @return string
	 */
	private function getImageSizeWithTypeUsageTranslation($entityName, $typeName, $sizeName) {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */

		return $translator->trans('Není zadáno pro entitu ' . $entityName . ', typ ' . $typeName . ' a rozměr ' . $sizeName);
	}
}
