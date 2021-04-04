<?php


namespace blackjack200\lunar\detection\movement;


use blackjack200\lunar\detection\DetectionBase;
use blackjack200\lunar\user\User;
use pocketmine\entity\Effect;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;

// https://github.com/Tecnio/AntiHaxerman/blob/master/src/main/java/me/tecnio/antihaxerman/check/impl/movement/flight/FlightD.java
class FlyD extends DetectionBase {
	private float $reward;

	public function __construct(User $user, string $name, $data) {
		parent::__construct($user, $name, $data);
		$this->reward = $this->getConfiguration()->getReward();
	}

	public function handleReceive(DataPacket $packet) : void {
		if ($packet instanceof MovePlayerPacket) {
			$user = $this->getUser();
			$player = $user->getPlayer();
			$info = $user->getMovementInfo();
			if (
				$info->checkFly &&
				!$info->inVoid &&
				$info->inAirTick > 15 + $user->getEffectLevel(Effect::JUMP) &&
				$info->timeSinceTeleport() > 0.5 &&
				$user->timeSinceHurt() > 0.75 &&
				$user->timeSinceJoin() > 5 &&
				!$player->isFlying()
			) {
				$acceleration = $info->moveDelta->y - $info->lastMoveDelta->y;
				if ($acceleration > 0 && $this->preVL++ > 1) {
					$this->addVL(1);
					if ($this->overflowVL()) {
						$this->fail("acceleration=$acceleration");
					}
				} else {
					$this->rewardPreVL($this->reward);
				}
			}
		}
	}
}