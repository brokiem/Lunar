<?php
declare(strict_types=1);

namespace blackjack200\lunar\detection\action;


use blackjack200\lunar\detection\DetectionBase;
use blackjack200\lunar\user\User;
use pocketmine\event\block\BlockBreakEvent;

class NukerA extends DetectionBase {
    protected int $maxBlock;
    private int $count = 0;

    public function __construct(User $user, string $name, string $fmt, ?string $webhookFmt, $data) {
        parent::__construct($user, $name, $fmt, $webhookFmt, $data);
        $this->maxBlock = $this->getConfiguration()->getExtraData()->MaxBlock;
    }

    /**
     * @param mixed $data
     */
    public function check(...$data): void {
        if (!isset($data[0])) {
            $this->count = 0;
            $this->VL *= $this->getConfiguration()->getReward();
        } else {
            $this->impl($data[0]);
        }
    }

    private function impl(BlockBreakEvent $event): void {
        $this->count++;
        if ($this->count >= $this->maxBlock) {
            $this->addVL(1, "COUNT=$this->count");
            $event->setCancelled();
            if ($this->overflowVL()) {
                $this->fail("COUNT=$this->count");
            }
        }
    }
}