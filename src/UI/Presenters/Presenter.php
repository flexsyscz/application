<?php

declare(strict_types = 1);

namespace Flexsyscz\Application\UI\Presenters;

use Flexsyscz\FlashMessages\FlashMessages;
use Flexsyscz\Localization\Translations\TranslatedComponent;
use Nette;


/**
 * @property-read Template  $template
 */
abstract class Presenter extends Nette\Application\UI\Presenter implements Nette\Security\Resource
{
	use FlashMessages;
	use TranslatedComponent;


	public function getResourceId(): string
	{
		return static::class;
	}


	public function beforeRender()
	{
		parent::beforeRender();

		$this->template->setTranslator($this->translatorNamespace);
	}
}
