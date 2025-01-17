<?php

declare(strict_types=1);

namespace Flexsyscz\Application\UI\Accessory;

use Nette\Utils\Strings;


trait JsScope
{
	protected function setJsScope(?string $scope = null): void
	{
		$scope = $scope ?? explode(':', $this->getName());
		array_walk($scope, fn(&$value) => $value = Strings::firstLower($value));

		$this->template->scope = implode('.', $scope);
	}
}
