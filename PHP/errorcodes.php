<?php
    enum ErrorCodes: int
    {
        case DatabaseConnectionError = 0;
        case LoginFailedCredentials = 1;
        case UserNotFoundError = 2;
        case ListNotFoundError = 3;
        case ItemNotFoundError = 4;
        case InvitationNotFoundError = 5;
        case UseAlreadyMemberError = 6;
        case UserAlreadyAdminError = 7;
        case UserAlreadyInvitedError = 8;
        case DeleteError = 9;
        case UserNotMemberError = 10;
        case UserNotAdminError = 11;
        case ConflictError = 12;
    }

    function print_error($errorCode, $errorMsg){
        echo "Error: " . $errorCode . " - " . $errorMsg;
    }
?>