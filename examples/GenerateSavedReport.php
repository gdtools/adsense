<?php
/*
 * Copyright 2014 Google Inc.
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

/**
 * Retrieves a report for the specified ad client.
 *
 * Tags: accounts.reports.saved.generate
 */
class GenerateSavedReport {
  /**
   * Retrieves a report for the specified ad client.
   *
   * @param $service Google_Service_AdSense AdSense service object on which to
   *     run the requests.
   * @param $accountId string the ID for the account to be used.
   * @param $savedReportId string the saved report ID on which to run the
   *     report.
   */
  public static function run($service, $accountId, $savedReportId) {
    $separator = str_repeat('=', 80) . "\n";
    // print $separator;
    // printf("Running saved report %s on account %s\n", $savedReportId,
    //     $accountId);
    // print $separator;

    // Run report.
    $report =
        $service->accounts_reports_saved->generate($accountId, $savedReportId);
    if (isset($report) && isset($report['rows'])) {

      echo '<table class="table table-bordered table-condensed" id="table1">
                        <thead>
                            <tr>';
      
      foreach($report['headers'] as $header) {
          echo '<th>'.to1($header['name']).'</th>';
      }
      echo '</tr>
                        </thead>
                        <tbody>
                            <tr>';

      // Display results.
      foreach($report['rows'] as $row) {
        foreach($row as $column) {

          echo '<td>'.$column.'</td>';
        }

      }
      
      echo ' </tr>
                        </tbody>
                    </table>';
    } 


  }
}
  function  to1($s){
      switch ($s) {
        case 'DATE':     return  '日期';  break;
        case 'PAGE_VIEWS':     return  '流量';  break;
        case 'AD_REQUESTS':     return  '请求';  break;
        case 'AD_REQUESTS_COVERAGE':     return  '到达/请';  break;
        case 'CLICKS':     return  '点击';  break;
        case 'AD_REQUESTS_CTR':     return  '点击/请';  break;
        case 'COST_PER_CLICK':     return  '单价';  break;
        case 'AD_REQUESTS_RPM':     return  '刀/K请';  break;
        case 'EARNINGS':     return  '收入';  break;
        case 'PAGE_VIEWS_RPM':     return  '刀/K流';  break;
        case 'IMPRESSIONS':     return  '展量';  break;
        case 'IMPRESSIONS_RPM':     return  '刀/K展';  break;
        default: return $s;
      }
      
  }
