<?php

namespace PancakeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PancakeBundle extends Bundle
{
	public function getParent()
	{
		return 'FOSUserBundle';
	}
}
