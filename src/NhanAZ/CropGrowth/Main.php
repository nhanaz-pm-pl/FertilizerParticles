<?php

declare(strict_types=1);

namespace NhanAZ\CropGrowth;

use NhanAZ\CropGrowth\Blocks\ClayBlock;
use NhanAZ\CropGrowth\Blocks\CoarseDirt;
use NhanAZ\CropGrowth\Blocks\DirtBlock;
use NhanAZ\CropGrowth\Blocks\GrassBlock;
use NhanAZ\CropGrowth\Blocks\GravelBlock;
use NhanAZ\CropGrowth\Blocks\RedSandBlock;
use NhanAZ\CropGrowth\Blocks\RootedDirt;
use NhanAZ\CropGrowth\Blocks\SandBlock;
use NhanAZ\CropGrowth\Math\Math;
use NhanAZ\CropGrowth\Particle\CropGrowthParticle;
use NhanAZ\CropGrowth\Plants\Bamboo;
use NhanAZ\CropGrowth\Plants\BambooSapling;
use NhanAZ\CropGrowth\Plants\Beetroots;
use NhanAZ\CropGrowth\Plants\BrownMushroom;
use NhanAZ\CropGrowth\Plants\Carrots;
use NhanAZ\CropGrowth\Plants\Cocoa;
use NhanAZ\CropGrowth\Plants\DoubleTallgrassAndLargeFern;
use NhanAZ\CropGrowth\Plants\FernAndGrass;
use NhanAZ\CropGrowth\Plants\Flowers;
use NhanAZ\CropGrowth\Plants\MelonSeeds;
use NhanAZ\CropGrowth\Plants\Potatoes;
use NhanAZ\CropGrowth\Plants\PumpkinSeeds;
use NhanAZ\CropGrowth\Plants\RedMushroom;
use NhanAZ\CropGrowth\Plants\Saplings;
use NhanAZ\CropGrowth\Plants\SeaPickle;
use NhanAZ\CropGrowth\Plants\SugarCane;
use NhanAZ\CropGrowth\Plants\SweetBerryBush;
use NhanAZ\CropGrowth\Plants\TwistingVines;
use NhanAZ\CropGrowth\Plants\WeepingVines;
use NhanAZ\CropGrowth\Plants\Wheat;
use NhanAZ\CropGrowth\Sound\BoneMealUseSound;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Water;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

	protected function onEnable(): void {
		$this->registerEvents();
	}

	public static function onGrow(Block $block): void {
		$blockPos = $block->getPosition();
		$world = $blockPos->getWorld();
		$world->addParticle($blockPos, new CropGrowthParticle());
		$world->addSound(Math::center($blockPos), new BoneMealUseSound());
	}

	public static function isUseBoneMeal(Item $item, int $action): bool {
		if ($item->equals(VanillaItems::BONE_MEAL(), true) && $action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
			return true;
		}
		return false;
	}

	/** Check water inside the block itself (not supported on the API yet) */
	public static function isInWater(Block $block): bool {
		$blockPos = $block->getPosition();
		$world = $blockPos->getWorld();
		$hasWater = false;
		foreach ($blockPos->sides() as $vector3) {
			if ($world->getBlock($vector3) instanceof Water) {
				$hasWater = true;
				break;
			}
		}
		if ($hasWater) {
			return true;
		}
		return false;
	}

	/**
	 * @see https://minecraft.fandom.com/wiki/Plant#Other_Than_Plants
	 * @return array<Block>
	 */
	public static function aquaticPlants() {
		return [
			VanillaBlocks::WATER(), # [Exception]
			# Algae [https://minecraft.fandom.com/wiki/Plant#Algae]
			# TODO: Kelp

			# Animals [https://minecraft.fandom.com/wiki/Plant#Animals]
			VanillaBlocks::CORAL(),
			# VanillaBlocks::CORAL_BLOCK()
			VanillaBlocks::CORAL_FAN(),
			VanillaBlocks::SEA_PICKLE()
		];
	}

	private function registerEvent(Listener $event): void {
		$this->getServer()->getPluginManager()->registerEvents($event, $this);
	}

	/**
	 * @see https://minecraft.fandom.com/wiki/Bone_Meal#Fertilizer
	 */
	private function registerEvents(): void {
		$this->registerEvent(new Wheat());
		$this->registerEvent(new Carrots());
		$this->registerEvent(new Potatoes());

		$this->registerEvent(new Beetroots());

		# [https://minecraft.fandom.com/wiki/Bamboo#Data_values]
		$this->registerEvent(new Bamboo());
		$this->registerEvent(new BambooSapling());

		$this->registerEvent(new MelonSeeds());
		$this->registerEvent(new PumpkinSeeds());

		# Saplings [https://minecraft.fandom.com/wiki/Sapling#Data_values]
		$this->registerEvent(new Saplings()); # [Oak Sapling, Spruce Sapling, Birch Sapling, Jungle Sapling, Acacia Sapling, Dark Oak Sapling]
		# TODO: Mangrove Propagule
		# TODO: Azalea
		# TODO: Flowering Azalea
		# TODO: Mangrove Propagule (not hanging)

		# Grass(s) [https://minecraft.fandom.com/wiki/Grass#Data_values]
		$this->registerEvent(new FernAndGrass()); # (Ferns)
		$this->registerEvent(new DoubleTallgrassAndLargeFern()); # (Tall Grass)
		# TODO: Seagrass

		# Mushrooms [https://minecraft.fandom.com/wiki/Mushroom#Data_values]
		$this->registerEvent(new BrownMushroom());
		$this->registerEvent(new RedMushroom());

		$this->registerEvent(new Cocoa());

		$this->registerEvent(new SweetBerryBush());

		$this->registerEvent(new SeaPickle());

		$this->registerEvent(new SugarCane());

		# TODO: Kelp

		# Flowers [https://minecraft.fandom.com/wiki/Flower]
		$this->registerEvent(new Flowers());
		# TODO: Wither Rose [Java Only]
		# Sunflower [Imported]
		# Lilac [Imported]
		# Rose Bush [Imported]
		# Peony [Imported]

		# TODO: Fungus

		$this->registerEvent(new WeepingVines());

		$this->registerEvent(new TwistingVines());

		# TODO: Cave Vines

		# TODO: Glow Lichen

		# TODO: Moss Block

		# TODO: Big Dripleaf
		# TODO: Small Dripleaf

		$this->registerEvent(new DirtBlock());
		$this->registerEvent(new RootedDirt());
		$this->registerEvent(new CoarseDirt());
		$this->registerEvent(new SandBlock());
		$this->registerEvent(new RedSandBlock());
		$this->registerEvent(new ClayBlock());
		$this->registerEvent(new GravelBlock());

		# TODO: Mangrove Leaves

		# Mangrove Propagule [Mentioned]

		$this->registerEvent(new GrassBlock());
	}
}
