<?php
/**
 * File comment.
 */
namespace com\mohiva\test\resources\common\lang;

use com\mohiva\test\resources\common\lang as constAlias;
use com\mohiva\test\resources\common\lang\annotations as nsAlias;
use com\mohiva\test\resources\common\lang\annotations\DoubleParameter;
use com\mohiva\test\resources\common\lang\annotations\NonParameter;
use com\mohiva\test\resources\common\lang\annotations\SingleParameter;
use com\mohiva\test\resources\common\lang\annotations\DefaultValue;
use com\mohiva\test\resources\common\lang\annotations\ArrayValue;
use com\mohiva\test\resources\common\lang\annotations\ObjectValue;
use com\mohiva\test\resources\common\lang\annotations\Nested;
use com\mohiva\test\resources\common\lang\annotations\Constant;
use com\mohiva\test\resources\common\lang\annotations\FuncGetArgs;
use com\mohiva\test\resources\common\lang\annotations\FuncGetArgs as FuncGetArgsAlias;
use com\mohiva\test\resources\common\lang\annotations\Value;
use com\mohiva\test\resources\common\lang\annotations\Error;
use com\mohiva\test\resources\common\lang\AnnotationTest as AnnotationTestAlias;

/**
 * Class comment.
 * 
 * @DoubleParameter(param1 = "pa\"r\"am1\"", param2 = "param\"2")
 * @DoubleParameter(param1 = "pa\'r\'am1\"", param2 = "param\'2")
 * @DoubleParameter(param1 = "pa'r'am1", param2 = 'param"2')
 * @DoubleParameter('pa\'r\'am1\'', 'param\'2')
 * @DoubleParameter('pa\"r\"am1\'', 'param\"2')
 */
class AnnotationTest {
	
	const PARAM1 = 1;
	const PARAM2 = 2;
	const PARAM3 = 2;
	const PARAM4 = 2;
	const PARAM5 = 2;
	
	/**
	 * Non parameter annotation.
	 * 
	 * @NonParameter
	 */
	private $nonParameter;
	
	/**
	 * Single parameter annotation.
	 * 
	 * @SingleParameter("param1")
	 * @SingleParameter(param1 = "param1")
	 */
	private $singleParameter;
	
	/**
	 * Default value annotation.
	 * 
	 * @DefaultValue(param1 = 'param1', param3 = 'param3')
	 * @DefaultValue(param3 = 'param3')
	 * @DefaultValue('param1', 'param2')
	 */
	private $defaultValue;
	
	/**
	 * Array value annotation.
	 * 
	 * @ArrayValue(['key':'value', 1, 3, 4, 'test', 1:3, false, null])
	 */
	private $arrayValue;
	
	/**
	 * Object value annotation.
	 * 
	 * @ObjectValue({key1:"value1", key2:2, key3:true, key4:false, key5:null})
	 */
	private $objectValue;
	
	/**
	 * Nested annotation.
	 * 
	 * @Nested(
	 *     @NonParameter,
	 *     @DefaultValue(param1 = 'param1', param3 = 'param3'),
	 *     @ArrayValue(['key':'value', 1, 3, 4, 'test', 1:3, false, null]),
	 *     @ObjectValue({key1:"value1", key2:2, key3:true, key4:false, key5:null}),
	 *     [1, 2, 3, 4, 5],
	 *     {key1:1, key2:2, key3:3},
	 *     true,
	 *     false, 
	 *     null
	 * )
	 */
	private $nested;
	
	/**
	 * Constant annotation.
	 * 
	 * @Constant(
	 *     self::PARAM1,
	 *     AnnotationTest::PARAM2,
	 *     AnnotationTestAlias::PARAM3,
	 *     constAlias\AnnotationTest::PARAM4,
	 *     \com\mohiva\test\resources\common\lang\AnnotationTest::PARAM5,
	 *     T_BREAK
	 * )
	 */
	private $constant;
	
	/**
	 * Use the function func_get_args().
	 * 
	 * @FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 * @FuncGetArgs(1, 2, 3, 4, 5)
	 */
	private $funcGetArgs;
	
	/**
	 * Test all possible values.
	 * 
	 * @Value(true, false, null, -1, 1, -1.1, 1.1, "test", "test\"test", 'test\'test', {}, [], @NonParameter)
	 */	
	public function value() {}
	
	/**
	 * Use the namespace of this class.
	 * 
	 * @annotations\FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 */
	public function classNamespace() {}
	
	/**
	 * Namespace alias for the FuncGetArgs annotation.
	 * 
	 * @nsAlias\FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 */
	public function namespaceAlias() {}
	
	/**
	 * Class alias for the FuncGetArgs annotation.
	 * 
	 * @FuncGetArgsAlias(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 */
	public function classAlias() {}
	
	/**
	 * Fully qualified name.
	 * 
	 * @\com\mohiva\test\resources\common\lang\annotations\FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 */
	public function fullyQualifiedName() {}
	
	/**
	 * @abstract
	 * @access
	 * @author
	 * @category
	 * @copyright
	 * @deprecated
	 * @example
	 * @final
	 * @filesource
	 * @global
	 * @ignore
	 * @internal
	 * @license
	 * @link
	 * @method
	 * @name
	 * @package
	 * @param
	 * @property
	 * @return
	 * @see
	 * @since
	 * @static
	 * @staticvar
	 * @subpackage
	 * @todo
	 * @tutorial
	 * @uses
	 * @var
	 * @version
	 * 
	 * A text with inline annotations {@example} {@id} {@internal} {@inheritdoc} {@link} {@source} {@toc} {@tutorial}
	 */
	public function phpDocumentor() {}
	
	/**
	 * @Error(TEST)
	 */
	public function undefinedConstant() {}
	
	/**
	 * @Error(AnnotationTest::Error)
	 */
	public function undefinedClassConstant() {}
	
	/**
	 * @Error(NotExisting::Error)
	 */
	public function classForClassConstantNotFound() {}
	
	/**
	 * @NotDefined()
	 */
	public function classForAnnotationNotFound() {}
	
	/**
	 * @Error(1, param1 = 'param1')
	 */
	public function mixingNamedAndUnnamedParams() {}
	
	/**
	 * @Error(param1 = 'param1', undefined = 1, param2 = 'param2', param3 = 'param3')
	 */
	public function undefinedParam() {}
	
	/**
	 * @Error(1, 2)
	 */
	public function undefinedParameterValue1() {}
	
	/**
	 * @Error(param1 = 'param2', param3 = 'param3')
	 */
	public function undefinedParameterValue2() {}
	
	/**
	 * @Error()
	 */
	public function undefinedParameterValue3() {}
	
	/**
	 * @Error(
	 */
	public function syntaxError1() {}
	
	/**
	 * @Error(:
	 */
	public function syntaxError2() {}
	
	/**
	 * @Error({
	 */
	public function syntaxError3() {}
	
	/**
	 * @Error({[
	 */
	public function syntaxError4() {}
	
	/**
	 * @Error({=
	 */
	public function syntaxError5() {}
	
	/**
	 * @Error({key::
	 */
	public function syntaxError6() {}
	
	/**
	 * @Error({key=
	 */
	public function syntaxError7() {}
	
	/**
	 * @Error(['key'::
	 */
	public function syntaxError8() {}
	
	/**
	 * @Error(['key'=
	 */
	public function syntaxError9() {}
	
	/**
	 * @Error({'key'::
	 */
	public function syntaxError10() {}
	
	/**
	 * @Error({'key'=
	 */
	public function syntaxError11() {}
	
	/**
	 * @Error({key:}
	 */
	public function syntaxError12() {}
	
	/**
	 * @Error(['key':]
	 */
	public function syntaxError13() {}
	
	/**
	 * @Error(['key':'test'][
	 */
	public function syntaxError14() {}
	
	/**
	 * @Error({key:'test'}{
	 */
	public function syntaxError15() {}
	
	/**
	 * @Error(['key':'test'],
	 */
	public function syntaxError16() {}
	
	/**
	 * @Error({key:'test'},:
	 */
	public function syntaxError17() {}
	
	/**
	 * @Error({key:'test'}test
	 */
	public function syntaxError18() {}
	
	/**
	 * @Error({key:'test'}'test'
	 */
	public function syntaxError19() {}
	
	/**
	 * @Error({key:'test'}-1
	 */
	public function syntaxError20() {}
}
