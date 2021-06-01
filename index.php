<!DOCTYPE html>
<html>
    <head>
        <title>Credissimo Plus credit</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <style>
            .label_light_blue{
                background-color: #d7e3f7;
                padding-left:15px;
            }
            .label_dark_blue{
                background-color: #86aef1;
                padding-left:15px;
            }
            .main_label{
                margin-top: 10px;
                color:#1a106d;
                font-weight: bold;
                font-size:16px;
            }
            <style> 
        </style> 
    </head>
    <body>
        <div class = "container">
            <labe ><h2>Plus credit</h2></label>
                <form action="" method="post">
                    <div>
                        <label for = "installments" class="main_label">Installments (between 3 and 24)</label><span class="error"><?= isset($error_msg['installments']) ? $error_msg['installments'] : "" ?></span>
                        <input type="number" name="installments" value="<?= $_POST['installments'] ?>" class="form-control" placeholder="installments" pattern="[0-9]{1,2}" min=3 max=24 required />

                    </div>	
                    <div>
                        <label for = "amount" class="main_label">Amount (between 500 and 5000)</label><span class="error"><?= isset($error_msg['amount']) ? $error_msg['amount'] : "" ?></span>
                        <input type="number" name="amount" value="<?= $_POST['amount'] ?>" class="form-control" placeholder="amount" pattern="[0-9]{3,4}" min=500 max=5000 required>

                    </div>
                    <div>
                        <label for="maturity_date" class="main_label">Maturity date</label><span class="error"><?= isset($error_msg['maturity_date']) ? $error_msg['maturity_date'] : "" ?></span>

                        <div class="radio">
                            <label class="ml-1"><input type="radio" name="maturity_date" value="10" <?= (isset($_POST['maturity_date']) && $_POST['maturity_date'] == 10) ? "checked" : "" ?> required > 10</label>
                        </div>
                        <div class="radio">
                            <label class="ml-1"><input type="radio" name="maturity_date" value="20" <?= (isset($_POST['maturity_date']) && $_POST['maturity_date'] == 20) ? "checked" : "" ?> required > 20</label>
                        </div>
                        <div class="radio">
                            <label class="ml-1"><input type="radio" name="maturity_date" value="<?= date('t', strtotime('today')) ?>" <?= (isset($_POST['maturity_date']) && $_POST['maturity_date'] == date('t', strtotime('today'))) ? "checked" : "" ?> required > End of month</label>
                        </div>
                    </div>

                    <div>
                        <label for = "utilisation_date" class="main_label">Utilisation date</label><span class="error"><?= isset($error_msg['utilisation_date']) ? $error_msg['utilisation_date'] : "" ?></span>
                        <input type="date" name="utilisation_date" value="<?= $_POST['utilisation_date'] ?>" class="form-control" value="<?= date('Y-m-d', strtotime('today')) ?>"  required>
                    </div>

                    <div>
                        <label class="main_label">Note: </label>
                        <p>Annual interest rate 10%.</p>
                        <p>Additional taxes: 25 лв.</p>
                        <p>Additional taxes: 17 лв.</p>
                    </div>

                    <input type="submit" name="save" value="generate payment schedule" class="btn btn-success" />
                </form>

                <hr />

                <?php
                include "plusCredit.php";

                if (isset($_POST['save'])) {
                    $request = $_POST;
                    $credit_data = new plusCredit($request);

                    $show_data = $credit_data->schedule;

                    foreach ($show_data as $key => $month_data) {
                        foreach ($month_data as $label => $value) {
                            ?>
                            <div class="row m-1">
                                <div class="col-sm-4 label_light_blue p2"><?= $label ?></div>
                                <div class="col-sm-8 label_dark_blue"><?= $value ?></div>
                            </div>
                            <?php
                        }
                        echo "<hr />";
                    }
                }
                ?>
        </div>
    </body>
    <footer>
    </footer>
</html>
