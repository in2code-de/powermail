<?php
declare(strict_types = 1);
namespace In2code\Powermail\Tca;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Adds a new eval possibility to TCA of TYPO3
 */
class EvaluateEmail
{

    /**
     * Adds new JavaScript function for evaluation of the TCA fields in backend
     *
     * @return string
     */
    public function returnFieldJS(): string
    {
        return '
			if (value === "" || validEmail(value)) {
				return value;
			} else {
				return "errorinemail@tryagain.com"
			}

			/**
			 * Check if given String is a valid Email address
			 *
			 * @param	string		An email address
			 * @return	boolean
			 */
			function validEmail(s) {
				var a = false;
				var res = false;
				if(typeof(RegExp) == "function") {
					var b = new RegExp("abc");
					if(b.test("abc") == true){a = true;}
				}
					if (a == true) {
					reg = new RegExp("^([a-zA-Z0-9\\-\\.\\_]+)" + "(\\@)([a-zA-Z0-9\\-\\.]+)" + "(\\.)([a-zA-Z]{2,4})$");
					res = (reg.test(s));
				} else {
					res = (s.search("@") >= 1 && s.lastIndexOf(".") > s.search("@") && s.lastIndexOf(".") >= s.length-5);
				}
				return(res);
			}
		';
    }

    /**
     * Server valuation
     *
     * @param string $value The field value to be evaluated.
     * @param string $isIn The "isIn" value of the field configuration from TCA
     * @param bool $set defining if the value is written to the database or not.
     * @return string
     */
    public function evaluateFieldValue(string $value, string $isIn, bool &$set): string
    {
        unset($isIn);
        if (GeneralUtility::validEmail($value)) {
            $set = 1;
        } else {
            $set = 0;
            $value = 'errorinemail@tryagain.com';
        }
        return $value;
    }
}
