<?php

namespace Common\Latte;

use Nette\Utils\Html;

class ImageMacro extends \Latte\Macros\MacroSet
{
	public static function install(\Latte\Compiler $compiler)
	{
		$set = new static($compiler);

		// predelat na:
		// volani klasicky pole {img src=>$image->getUrl(),class=>'aaaa'}
		// echo Common\ImageMAcro::render($baseUrl, %args);
		// replace
		// data-src
		// unitr Html::el( ,[]) . Html::el()
		
		$set->addMacro('image', function (\Latte\MacroNode $node, \Latte\PhpWriter $writer) {
			$args = \explode(', ', $node->args);
			
			if ($args < 1) {
				throw new Latte\CompileException('Too few arguments arguments!');
			}

			$parametersState = [
				'src' => true,
				'class' => false,
				'alt' => true,
				'style' => false
			];

			$parametersValues = (object)[
				'src' => null,
				'class' => null,
				'alt' => null,
				'style' => null
			];

			foreach ($args as $arg) {
				[$key, $value] = \explode('=', $arg);
				if (\array_search($key, \array_keys($parametersState)) === false) {
					throw new Latte\CompileException("Unknown parameter '$key'!");
				}

				$parametersState[$key] = false;
				$parametersValues->$key = $value;
			}

			foreach ($parametersState as $key => $value) {
				if ($value) {
					throw new Latte\CompileException("Required parameter '$key' missing!");
				}
			}
		
			$phpCode = 'echo "<img ';
			$phpCode .= 'src=\'{$presenter->template->pubUrl}/img/image-placeholder.png\' ';
			$phpCode .= "data-src=$parametersValues->src ";
			$phpCode .= "alt=$parametersValues->alt ";
			$phpCode .= 'loading=\"lazy\" ';

			if ($parametersValues->class) {
				$phpCode .= "class=$parametersValues->class ";
			}

			if ($parametersValues->style) {
				$phpCode .= "style=$parametersValues->style ";
			}

			$phpCode .= '>';

			$phpCode .= '<noscript><img ';
			$phpCode .= "src=$parametersValues->src ";
			$phpCode .= "alt=$parametersValues->alt ";
			$phpCode .= 'loading=\"lazy\" ';
			$phpCode .= '></noscript>';
			$phpCode .= '"';

			return $writer->write($phpCode);
		});

		return $set;
	}
}