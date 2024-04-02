<?php
enum ErrorCodes: int
{
    case PasswordMismatchError = 1;
    case UserNotFoundError = 2;
    
    case DatabaseConnectionError = 40624;
    case UnknownHostError = 11001;
    case LoginFailedError = 18456;
    case UniqueKeyError = 2627;
}
?>