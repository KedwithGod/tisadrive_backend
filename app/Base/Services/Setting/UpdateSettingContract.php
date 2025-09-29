<?php

namespace App\Base\Services\Setting;

use App\Models\Setting;
use Exception;

interface UpdateSettingContract {
	/**
	 * The setting model instance.
	 *
	 * @var Setting
	 */
	public function softupdate();
}
