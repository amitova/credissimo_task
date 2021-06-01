# credissimo_task

The project contains 2 files. 
1. plusCredit.php 
The file contains PlusCredit class, that generates payment schedule by input parameters.

2. index.php
The file contains data view and includes plusCredit.php

You should load index.php file to start working with the program.
For more info about how the program works, you can see the comments in the program code.

TASK

Credissimo BackEnd Developer
Hello and nice to meet you! In this document you'll find the development task which needs to be accomplished before the next step.

Task Overview
Imagine you are part of a company offering credits. Company needs for its customers new product called Plus Credit.

Task Goal
Create a library that will generate payment schedule for a Plus Credit.

Input params
Number of installments - allowed [3-24] number of installments.
Credit amount - allowed [500-5000] leva.
Annual interest rate - persent (10 means 10%).
Maturity date - allowed days are 10,20 or EOM. format (2021-03-31). EOM means end of month.
Utilisation date - format (2021-03-16). This is the credit start date.
Additional taxes - array with a tax name and a tax amount. Taxes are distributed equaly among all installments.
Input example

$params = [
    'numberOfInstallments' => 3,
    'amount' => 500,
    'air' => 10,
    'maturityDate' => '2021-03-31',
    'utilisationDate' => '2021-03-16',
    'taxes' => [
        'tax1' => 25,
        'tax2' => 17'
    ]
];
Output params
Payment schedule with annuity installments
Output example

[
     0 => [
        'number' => 1,
        'date' => '2021-03-31',
        'period' => 16,
        'installmentAmount' => 183.45,
        'principal' => 165.29,
        'interest' => 4.16,
        'tax1' => 8.33,
        'tax2' => 5.67,
     ],
     1 => [
        'number' => 2,
        'date' => '2021-04-30',
        'period' => 30,
        'installmentAmount' => 183.45,
        'principal' => 166.66,
        'interest' => 2.79,
        'tax1' => 8.33,
        'tax2' => 5.67,
     ],
     2 => [
        'number' => 3,
        'date' => '2021-05-31',
        'period' => 31,
        'installmentAmount' => 183.45,
        'principal' => 168.05,
        'interest' => 1.40,
        'tax1' => 8.34,
        'tax2' => 5.66,
     ], 
]
All differences from roundings go to last installment.

Requirements
PHP, choose your favorite version.
Third party libraries, dependencies, tools, framework etc. can be used. Use composer for autoloading, use PSR-4.
Code must be PSR-1 and PSR-2 compatible.
Minimal documentation should be provided - how to run, how to initiate tests.
Final Words
This task should be accomplished in the next 7 days and sent to the respective Credissimo's representative, through a link pointed to a GitHub or Bitbucket public repository. Happy coding!
