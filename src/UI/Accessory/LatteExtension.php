<?php

declare(strict_types=1);

namespace Flexsyscz\Application\UI\Accessory;

use Flexsyscz\Application\Exceptions\AssetNotFoundException;
use Flexsyscz\Application\Exceptions\FileNotFoundException;
use Flexsyscz\FileSystem\Directories\AssetsDirectory;
use Flexsyscz\FileSystem\Directories\DataDirectory;
use Latte\Extension;
use Nette\Http\Request;
use Nette\Utils\FileSystem;


class LatteExtension extends Extension
{
	public function __construct(
		private readonly Request $httpRequest,
		private readonly AssetsDirectory $assetsDirectory,
		private readonly DataDirectory $dataDirectory,
	)
	{
	}


	public function getFilters(): array
	{
		return [
			'humanFileSize' => $this->filterHumanFileSize(...),
		];
	}


	public function getFunctions(): array
	{
		return [
			'asset' => $this->asset(...),
			'data' => $this->data(...),
		];
	}


	public function filterHumanFileSize(int $size, int $precision = 2): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];

		$bytes = max($size, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . $units[$pow];
	}


	public function asset(string $path): string
	{
		if ($this->assetsDirectory->exists($path)) {
			$absolutePath = FileSystem::normalizePath($this->assetsDirectory->getAbsolutePath($path));
			$hash = hash_file('sha256', $absolutePath);

			$path = FileSystem::normalizePath($this->assetsDirectory->getRelativePath($path));
			$path = str_replace('\\', '/', $path);
			$basePath = $this->httpRequest->getUrl()->getBasePath();
			$httpRelativePath = sprintf('%s/%s?hash=%s', $basePath === '/' ? '' : $basePath, $path, $hash);

			return $this->httpRequest->getUrl()->getHostUrl() . preg_replace('#/+#', '/', $httpRelativePath);
		}

		throw new AssetNotFoundException(sprintf("Asset '%s' not found.", $path));
	}


	public function data(string $path): string
	{
		if ($this->dataDirectory->exists($path)) {
			$absolutePath = FileSystem::normalizePath($this->dataDirectory->getAbsolutePath($path));
			$hash = hash_file('sha256', $absolutePath);

			$path = FileSystem::normalizePath($this->dataDirectory->getRelativePath($path));
			$path = str_replace('\\', '/', $path);
			$basePath = $this->httpRequest->getUrl()->getBasePath();
			$httpRelativePath = sprintf('%s/%s?hash=%s', $basePath === '/' ? '' : $basePath, $path, $hash);

			return $this->httpRequest->getUrl()->getHostUrl() . preg_replace('#/+#', '/', $httpRelativePath);
		}

		throw new FileNotFoundException(sprintf("File '%s' not found.", $path));
	}
}
