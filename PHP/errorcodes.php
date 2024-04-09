<?php
    enum ErrorCodes: int
    {
        case DatabaseConnectionError = 0;
        case LoginFailedCredentials = 1;
        case UserNotFoundError = 2;
        case ListNotFoundError = 3;
        case ItemNotFoundError = 4;
        case InvitationNotFoundError = 5;
        case UserAlreadyMember = 6;
        case UserAlreadyAdmin = 7;
        case DeleteError = 8;
        case UserNotMemberError = 9;
        case UserNotAdminError = 10;
    }

    function print_error($errorCode, $errorMsg){
        echo "Error: " . $errorCode . " - " . $errorMsg;
    }
?>