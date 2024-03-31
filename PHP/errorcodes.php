<?php
enum ErrorCodes: int
{
    case PasswordMismatchError = 1;
    case UserNotFoundError = 2;
    
    case UnknownHostError = 11001;
    case LoginFailedError = 18456;
    case UniqueKeyError = 2627;
}
?>