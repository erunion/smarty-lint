<?php
/* Copyright 2005-2008 Andrew A. Bakun
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class example_extensions {
  static public function prefilter_convert_loop_breaks($tplsource, &$smarty) {
    return preg_replace('/\{break\}/', '{php}break;{/php}', $tplsource);
  }

  static public function prefilter_convert_loop_continue($tplsource, &$smarty) {
    return preg_replace('/\{continue\}/', '{php}continue;{/php}', $tplsource);
  }

  static public function dump_array($value, $level = -1, $html = 1) {
    $x = var_export($value, true);
    if ($html) {
      $x = "<pre>" . htmlspecialchars($x) . "</pre>";
    }

    return $x;
  }

  static public function modifier_printr($v) {
    return smarty_extensions::dump_array($v, -1, 0); # no html
  }
}
