<?php
	Class extension_country_code_extractor extends Extension{
				
		var $phoneCodes;

		public function getSubscribedDelegates(){
			return array(
			
				// Used splitting a phone number into two parts
				array(
					'page'      => '/frontend/',
					'delegate'  => 'DataSourcePostExecute',
					'callback'  => 'processDS',
				)

			);
		}

		private function numberFilter($countryList,&$number,&$position){
			$result = array_filter($countryList, function($array) use(&$number,&$position){ // use($number,$position)
						return substr($array, 0, $position+1) === substr($number, 0, $position+1);
					});

			//if not found return false
			if(empty($result) || $position > 3){
				return false;
			} elseif(count($result)==1){
				//when found return the result
				return current($result);
			}else{
				//we should go deeper as there are still multiple elements left
				return $this->numberFilter($result,$number,$position+=1);
			}
		}

		private function stripNumber($number){
			$number = preg_replace("/[^0-9]/", "", $number);
			if (strpos($number, '00') === 0){
				$number = substr($number, 2);
			}
			return $number;
		}

		private function replacePhone(&$xmlObject,$xpath){
			$phone = $this->stripNumber((string)current($xmlObject->xpath($xpath)));
			if (empty($phone)) return;
			// $phone = $this->stripNumber("+35621212121");

			$phoneCode = $this->numberFilter($this->phoneCodes,$phone,$position = 0);
			$phone = substr($phone, strlen($phoneCode));
			$xmlObject->{$xpath} = $phone;
			$xmlObject->{$xpath}->addAttribute('country-code', $phoneCode);
		}

		/**
		 * Add the custom parameters before entry save. This will ensure we can track without passing data with post
		 */
		public function processDS($context){
			$this->phoneCodes = include EXTENSIONS . '/frontend_tracking/lib/country/phone_codes.php';

			//get list of datasources / nodes where the countrycode decode has to occur
			$settings = Symphony::Configuration()->get('country_code_extractor');

			//generate current datasource handle by removing class prefix
			$datasource = substr(get_class($context['datasource']),10);
			if (array_key_exists($datasource,$settings)){
				$xml = $context['xml']->generate();
				$xmlObject = simplexml_load_string($xml);

				foreach (explode(",",$settings[$datasource]) as $value) {
					$this->replacePhone($xmlObject,$value);
				}
				$generated = $xmlObject->asXML();
				$context['xml'] = substr($generated, strpos($generated, '?>') + 2);;
			}
		}
		
		public function enable(){
			return $this->install();
		}

		public function disable(){
		}

		public function install(){
		}

		public function uninstall(){
		}

	}

?>