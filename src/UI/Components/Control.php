<?php

declare(strict_types=1);

namespace Flexsyscz\Application\UI\Components;

use Flexsyscz\FlashMessages\FlashMessages;
use Flexsyscz\Localization\Translations\TranslatedComponent;
use Nette;


abstract class Control extends Nette\Application\UI\Control implements Nette\Security\Resource
{
	use FlashMessages;
	use TranslatedComponent;


	public function getResourceId(): string
	{
		return static::class;
	}


	public function createTemplate(?string $class = null): Nette\Application\UI\Template
	{
		$template = parent::createTemplate($class);
		if ($template instanceof Nette\Bridges\ApplicationLatte\Template) {
			$template->setTranslator($this->translatorNamespace);
		}

		return $template;
	}
}
