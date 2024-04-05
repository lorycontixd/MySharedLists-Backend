<?php
    enum ErrorCodes: int
    {
        case DatabaseConnectionError = 0;
        case LoginFailedCredentials = 1;
        case UserNotFoundError = 2;
        case ListNotFoundError = 3;
        case ItemNotFoundError = 4;
        case UserAlreadyMember = 5;
        case UserAlreadyAdmin = 6;
        case DeleteError = 7;
        case UserNotMemberError = 8;
        case UserNotAdminError = 9;
    }

    function print_error($errorCode, $errorMsg){
        echo "Error: " . $errorCode . " - " . $errorMsg;
    }
?>