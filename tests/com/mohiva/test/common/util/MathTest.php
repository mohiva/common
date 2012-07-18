<?php
/**
 * Mohiva Common
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.textile.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/mohiva/common/blob/master/LICENSE.textile
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\util;

use com\mohiva\common\util\Math;

/**
 * Unit test case for the `Math` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class MathTest extends \PHPUnit_Framework_TestCase {

	/**
	 * A list with numbers in different formats.
	 *
	 * @var array
	 */
	private $numbers = [
		[
			'hex' => '0',
			'dec' => '0'
		],
		[
			'hex' => 'f',
			'dec' => '15'
		],
		[
			'hex' => '0000000000000000000000000000000000000000',
			'dec' => '0'
		],
		[
			'hex' => 'ffffffffffffffffffffffffffffffffffffffff',
			'dec' => '1461501637330902918203684832716283019655932542975'
		],
		[
			'hex' => '33b6cb35690a5d00da1a573ad47b01cf49f1d93d',
			'dec' => '295234966906780732145051897383775895640213674301'
		],
		[
			'hex' => '503296496677587a87d093316cdbb23db6ca7624',
			'dec' => '457847390745537175817373762155941454868348827172'
		],
		[
			'hex' => 'e2893c609fc1140f7076fd6442db0424f51d601e',
			'dec' => '1293292375914999329366891726929931341564263358494'
		],
		[
			'hex' => '02a270b7fd3047844c0b3baed3f908ebb4e0c47c',
			'dec' => '15040521448052507392285449287455945702328288380'
		],
		[
			'hex' => '0e5fbd14edcef040e7e8c06feb857fd5103fb5b1',
			'dec' => '82060912929184124717658319210876077467882206641'
		],
		[
			'hex' => 'ac7baea8dd7df409932389f216fb490c39932521',
			'dec' => '984704619240722807657405290012906499058760361249'
		],
		[
			'hex' => 'bf1fd9ced34f34023bf0985d22d80c518aa190de',
			'dec' => '1091127534073600929557511571541905718742723170526'
		],
		[
			'hex' => '9cde40ed5748b4072e3194d149b95fe3fbe606a3',
			'dec' => '895558981631833689445716833162639457188674143907'
		],
		[
			'hex' => '148d4eaaf8c96280bf605fd9682d3003299f79ba',
			'dec' => '117331073426468854706195382183088988429666449850'
		],
		[
			'hex' => 'f00ab6e9786e44079003131589b058468c0b02d1',
			'dec' => '1370396726331617756942570040385268061380918575825'
		],
		[
			'hex' => '369634d3d794c500ea3ee604dd4947630c7ad5f9',
			'dec' => '311635215329271274366669060832253296361682425337'
		],
		[
			'hex' => '98504655ac386b41e5b6ff4ecd5c1c7006a4d64b',
			'dec' => '869556783794042628369630316994836335426487506507'
		],
		[
			'hex' => 'd0851fba70b88c1bb9d182403ec854a492a4c71a',
			'dec' => '1190438843365979085664412319214682863963388299034'
		],
		[
			'hex' => 'db14fa238eafe4ded23335c4b1d123e146cbf0da',
			'dec' => '1250736783885420919959194221524151948059989504218'
		],
		[
			'hex' => '40dba2c6628e4969e605bfb33fefea76303d27a2',
			'dec' => '370273452228436670271570383104773260708824295330'
		],
		[
			'hex' => '521664aa93c79d72e2d71f43842b019daa35c0b0',
			'dec' => '468636628874951043176188077808052097682255495344'
		],
		[
			'hex' => '2f939d778131c653d4cdcfa331c29b23ab01c6b3',
			'dec' => '271614493067126265752385183224870634118426248883'
		],
		[
			'hex' => '4fb2ee5b1d782fd2946832d3af351b52654b2c81',
			'dec' => '455000567269340579535595784869408796793700560001'
		],
		[
			'hex' => '0b246c620e0e1e31951cec44d2105f0ea3690067',
			'dec' => '63611166799444422055891737996303797119049334887'
		],
		[
			'hex' => '9f93ef44308efe970efdb2fc0beebe82931dff8b',
			'dec' => '911028585145258540929358953978783978708363247499'
		],
		[
			'hex' => '85b017888fcafcc3b623333f9a2e2f77bfdc387d',
			'dec' => '763222753726623952164969967245569615526134298749'
		],
		[
			'hex' => 'c2014a3f6c783d6db27ce4010f399b4ce7ad8475',
			'dec' => '1107572978176152409687636442036921363703656186997'
		],
		[
			'hex' => 'd45f608c8ddd12fed01503b0ecf333668e2262e9',
			'dec' => '1212433024816064236534069122350274302794820379369'
		],
		[
			'hex' => '976090a6a0fdcc7f722310fe2674eeeabba6d245',
			'dec' => '864211078803500068471749522339774881147082166853'
		],
		[
			'hex' => '00558fd4df0695674d7177610350caf18a4a5e3e',
			'dec' => '1908092835077159025941907322705420074678378046'
		]
	];

	/**
	 * Test if the `hexToDec` method calculates the correct decimal values for the given hexadecimal values.
	 */
	public function testHexToDec() {

		foreach ($this->numbers as $data) {
			$this->assertEquals($data['dec'], Math::hexToDec($data['hex']));
		}
	}

	/**
	 * Test if the `decToHex` method calculates the correct hexadecimal values for the given decimal values.
	 */
	public function testDecToHex() {

		foreach ($this->numbers as $data) {
			$this->assertEquals($data['dec'], Math::hexToDec($data['hex']));
		}
	}
}
