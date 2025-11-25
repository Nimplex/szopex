<?php

namespace App;

class FlashMessage
{
    public function set(string $msg, FlashMsgType $type): void
    {
        $_SESSION['_flashMessage'] = $msg;
        $_SESSION['_flashMessageType'] = $type->value;
    }

    /**
     * Alias to `set($msg, FlashMsgType::Ok)`; avoids an import
     */
    public function setOk(string $msg): void
    {
        $this->set($msg, FlashMsgType::Ok);
    }

    /**
     * Alias to `set($msg, FlashMsgType::Err)`; avoids an import
     */
    public function setErr(string $msg): void
    {
        $this->set($msg, FlashMsgType::Err);
    }

    /**
     * This function will only work if the exception has a non-zero code.
     */
    public function fromException(\Exception $e): void
    {
        if (!$e->getCode()) {
            throw new \ErrorException($e);
        }
        $msg = $e->getMessage();
        $this->set($msg, FlashMsgType::Err);
    }

    public function exists(): bool
    {
        return isset($_SESSION['_flashMessageType'], $_SESSION['_flashMessage']);
    }
    
    public function peekMsg(): ?string
    {
        return $_SESSION['_flashMessage'];
    }

    public function getMsg(): ?string
    {
        $msg = $this->peekMsg();
        unset($_SESSION['_flashMessage']);
        return $msg;
    }

    public function peekType(): ?FlashMsgType
    {
        return FlashMsgType::from((int) $_SESSION['_flashMessageType']);
    }

    public function getType(): ?FlashMsgType
    {
        $msg = $this->peekType();
        unset($_SESSION['_flashMessageType']);
        return $msg;
    }
}

enum FlashMsgType: int
{
    case Ok = 1;
    case Err = 2;
}
