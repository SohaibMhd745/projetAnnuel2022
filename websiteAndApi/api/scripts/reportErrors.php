<?php

/**
 * Reports on the missing parameter
 * @param string $param : param to report missing
 * @return void
 */
function reportMissingParam(string $param){
    echo formatResponse(400, ["Content-Type" => "application/json"],
        ["success" => false, "errorMessage" => "Missing parameter '".$param."'", "errorCode" => MISSING_PARAMETER, "parameter"=>$param]);
    die();
}

/**
 * Reports on invalid parameters
 * @param string $param : param to report as invalid
 * @return void
 */
function reportInvalidParam(string $param){
    echo formatResponse(400, ["Content-Type" => "application/json"],
        ["success" => false, "errorMessage" => "Invalid parameter value for param '".$param."'", "errorCode" => INVALID_PARAMETER, "parameter"=>$param]);
    die();
}

/**
 * Reports on invalid parameter length
 * @param string $param : param to report as invalid length
 * @return void
 */
function reportParamLength(string $param){
    echo formatResponse(400, ["Content-Type" => "application/json"],
        ["success" => false, "errorMessage" => "Parameter wrong length '".$param."'", "errorCode" => PARAMETER_WRONG_LENGTH, "parameter"=>$param]);
    die();
}