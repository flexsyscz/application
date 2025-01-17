<?php

declare(strict_types = 1);

namespace Flexsyscz\Application\UI\Presenters;

use Flexsyscz\Application\UI\Components\Control;
use Flexsyscz\FlashMessages\FlashMessage;
use Nette;


abstract class Template extends Nette\Bridges\ApplicationLatte\Template
{
	public Presenter $presenter;
	public Control|Nette\Application\UI\Control $control;

	/** @var FlashMessage[]|\stdClass[] */
	public array $flashes;
}
