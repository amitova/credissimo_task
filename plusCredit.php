<?php

class PlusCredit {

    /**
      Library that will generate payment schedule for a Plus Credit
     * */
    /*
      Input parameters:
     */
    //in a real project constans should be in another class
    const MIN_INSTALLMENTS = 3;
    const MAX_INSTALLMENTS = 24;
    const MIN_AMOUNT = 500;
    const MAX_AMOUNT = 5000;
    const ANNUAL_INTEREST = 10;
    const MATURITY_10 = 10;
    const MATURITY_20 = 20;

    //allowed [3-24] number of installments
    private $installments;
    //allowed  leva[500-5000]
    private $creditAmount;
    //percent
    private $annualInterest;
    //allowed days are 10,20 or EOM. format (2021-03-31). EOM means end of month.
    private $maturityDate;
    // This is the credit start date
    private $utilisationDate;
    
    // final data schedual
    public $schedule;
    
    //additional tax name and tax amount - percent
    private $additionalTaxes = [
        'tax1' => 25,
        'tax2' => 17,
    ];

    /*
      @construct - set input date to the properies
      param: data from html form
      return: bool
     */

    public function __construct($request) {
        $is_valid = $this->validateInputData($request);

        if ($is_valid == TRUE) {
            $i = 1;
            $this->installments = $request['installments'];
            $this->creditAmount = $request['amount'];
            $this->utilisationDate = $request['utilisation_date'];
            $this->maturityDate = $request['maturity_date'];

            $this->paymentSchedule();
        }
        return $is_valid;
    }

    /*
      @validateInputData
      return TRUE or error msg
      param: post request
     */

    private function validateInputData($request) {
        $is_valid = false;
        $error_msg = [];
        if (!empty($request)) {
            if ($request['installments'] >= 3 && $request['installments'] >= 24) {
                $is_valid = true;
            } else {
                $is_valid = false;
                $error_msg['installments'] = "Please insert Installments (between 3 and 24)";
            }

            if ($request['amount'] >= 500 && $request['amount'] >= 5000) {
                $is_valid = true;
            } else {
                $is_valid = false;
                $error_msg['amount'] = "Please insert Amount (between 500 and 5000)";
            }

            if (isset($request['maturity_date'])) {
                $is_valid = true;
            } else {
                $is_valid = false;
                $error_msg['maturity_date'] = "Please insert Maturity date!";
            }
            //need to by added - today is beffore utilisation_date
            if (isset($request['utilisation_date'])) {
                $is_valid = true;
            } else {
                $is_valid = false;
                $error_msg['utilisation_date'] = "Please insert correct Utilisation date!";
            }
        }
        if (!$is_valid) {
            return $error_msg;
        } else {
            return $is_valid;
        }
    }

    /*
      @paymentSchedule - create array with month schedule
     */

    private function paymentSchedule() {
        $new_amount = $this->creditAmount;

        for ($i = 1; $i <= $this->installments; $i++) {

            if ($i == 1) {
                $start_date = $this->utilisationDate;
                $end_date = $this->calcFirstPeriod();
            } else {
                $start_date = $end_date;

                //if maturity date == End of month

                if ($this->maturityDate != self::MATURITY_10 && $this->maturityDate != self::MATURITY_20) {
                    //$start_date = date_create($start_date);
                    //$end_date = date_format(date_add($start_date, date_interval_create_from_date_string('1 month')), 'Y-m-t');
                    $end_date = date("Y-m-d", strtotime("last day of next month", strtotime($start_date)));
                } else {

                    //$end_date = date_add($start_date, date_interval_create_from_date_string('1 month'));
                    $end_date = date("Y-m-d", strtotime("+1 month", strtotime($start_date)));
                }

                //$maturity_date = date_add(date_create($maturity_date), date_interval_create_from_date_string('1 month'));
            }

            $month_payment = $this->calcInterest($new_amount);
            $this->schedule[$i] = [
                'number' => $i,
                'installmentAmount' => 0,
                'principal' => $month_payment['principal'],
                'interest' => $month_payment['interest'],
                'date' => $end_date,
                'period' => $this->dateDiff($start_date, $end_date),
            ];

            $new_amount = $new_amount - $month_payment['principal'];

            foreach ($this->additionalTaxes as $key => $value) {
                if ($i == $this->installments) {
                    $month_tax[$key] = $this->calcLastMonthTaxes($key);
                } else {
                    $month_tax[$key] = $this->calcTaxes($key);
                }

                $this->schedule[$i][$key] = $month_tax[$key];
            }
            $sumTaxes = array_sum($month_tax);
            $this->schedule[$i]['installmentAmount'] = $month_payment['principal'] + $month_payment['interest'] + $sumTaxes;
        }
    }

    /*
      @calcFirstPeriod
      return TRUE or error msg
      param: maturity date , type string
     */

    private function calcFirstPeriod() {

        if ($this->maturityDate != self::MATURITY_10 && $this->maturityDate != self::MATURITY_20) {
            $next_period = 'last day next month';
            $date_format = 'Y-m-t';
        } else {
            $next_period = '1 month';
            $date_format = 'Y-m-d';
        }
        $start_date = date_create($this->utilisationDate);
        $utilisation_date = date_format($start_date, 'j');
        $end_day = $this->maturityDate;
        $end_date = date_create(date_format($start_date, 'Y-m-' . $end_day));

        if ($end_day <= $utilisation_date) {
            date_add($end_date, date_interval_create_from_date_string('1 month'));
            return date_format($end_date, 'Y-m-d');
        }
        //$month = $start_date;
        $month = date_format($start_date, 'm');
        $year = date_format($start_date, 'Y');

        return date_format(date_create($year . "-" . $month . "-" . $end_day), $date_format);
    }

    /*
      @dateDiff - calculate period between two dates
      param: begin date , end date
      return number, type integer
     */

    private function dateDiff($startDate, $endDate) {

        $start_date = date_create($startDate);
        $end_date = date_create($endDate);
        $period = date_diff($start_date, $end_date);

        return $period->days;
    }

    /*
      @calcInterest - calculate interest and principal
      param: $new_amount = amount - principal
      return interest and principal, type array
     */

    private function calcInterest($new_amount) {

        $year_intr = $new_amount * 10 / 100; //lv firs year

        $month_payment['interest'] = round(($new_amount * 10 / 100) / 12, 2); //lv first month
        //s = P / 100/12, P - годишен лихвен процент.
        $month_intr_percent = self::ANNUAL_INTEREST / 100 / 12;

        //AP = O * ps / 1 - (1 + ps) -s, O е сумата на основния дълг; ps - месечният лихвен процент на банката; S- броят на месеците в периода на кредита.
        $month_payment['principal'] = round(($this->creditAmount * $month_intr_percent) / (1 - pow((1 + $month_intr_percent), (- $this->installments))) - $month_payment['interest'], 2);

        return $month_payment;
    }

    /*
      @calcTaxes - calculate taxes for each month
      param: tax name
      return tax value, type float
     */

    private function calcTaxes($tax_name) {

        $tax = round($this->additionalTaxes[$tax_name] / $this->installments, 2, PHP_ROUND_HALF_DOWN);
        return $tax;
    }

    /*
      @calcLastMonthTaxes - calculate tax last month
      param: tax name
      return last month tax, type float
     */

    private function calcLastMonthTaxes($tax_name) {

        $tax = $this->calcTaxes($tax_name);
        $check_for_diff = $tax * $this->installments;
        $diff = $this->additionalTaxes[$tax_name] - $check_for_diff;
        $last_month_tax = $tax + $diff;

        return $last_month_tax;
    }

}
