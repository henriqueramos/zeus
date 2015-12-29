SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `zeus` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
USE `zeus` ;

-- -----------------------------------------------------
-- Table `zeus`.`groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`groups` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`groups` (
  `group_id` INT NOT NULL AUTO_INCREMENT ,
  `group_name` VARCHAR(255) NOT NULL ,
  `group_color` VARCHAR(6) NOT NULL DEFAULT 'FFFFFF' ,
  `group_icon` TEXT NULL ,
  `group_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`group_id`) ,
  UNIQUE INDEX `group_name_UNIQUE` (`group_name` ASC) )
ENGINE = InnoDB;

--
-- Extraindo dados da tabela `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `group_color`, `group_icon`, `group_date`) VALUES
(1, 'Asterisk', 'FFFFFF', 'icons/asterisk.png', '2015-10-26 03:42:24');

-- -----------------------------------------------------
-- Table `zeus`.`authors`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`authors` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`authors` (
  `author_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `author_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`author_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zeus`.`jobs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`jobs` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`jobs` (
  `job_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `job_name` VARCHAR(255) NOT NULL ,
  `job_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `job_cron` VARCHAR(255) NOT NULL ,
  `job_path` TEXT NOT NULL ,
  `job_type` ENUM('external','internal') NOT NULL DEFAULT 'internal' ,
  `job_author` INT(11) NOT NULL,
  `job_status` TINYINT(1) NOT NULL DEFAULT 1 ,
  `job_comment` TEXT NULL ,
  `job_group` INT(11) NOT NULL ,
  `is_running` TINYINT(1) NOT NULL DEFAULT 0 ,
  `last_run` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`job_id`) ,
  INDEX `fk_jobs_groups1_idx` (`job_group` ASC) ,
  INDEX `fk_jobs_authors1_idx` (`job_author` ASC) ,
  UNIQUE INDEX `job_name_UNIQUE` (`job_name` ASC) ,
  CONSTRAINT `fk_jobs_groups1`
    FOREIGN KEY (`job_group` )
    REFERENCES `zeus`.`groups` (`group_id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_jobs_authors1`
    FOREIGN KEY (`job_author` )
    REFERENCES `zeus`.`authors` (`author_id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zeus`.`alerts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`alerts` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`alerts` (
  `alert_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `job_id` INT(11) NOT NULL ,
  `alert_status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'O alerta está habilitado?' ,
  `alert_when` ENUM('http_code','total_time','redirect_count','content_type','content', 'others') NOT NULL DEFAULT 'http_code' COMMENT 'Qual será a chave usada para verificar asserções' ,
  `alert_comparison` ENUM('equal','not_equal','less_than','greater_than','less_than_or_equal_to','greater_than_or_equal_to') NOT NULL DEFAULT 'equal' COMMENT 'Qual será o operador de comparação usado para verificar asserções' ,
  `alert_return` TEXT NOT NULL COMMENT 'Qual será o valor usado para comparar?' ,
  `alert_type` ENUM('popup','email','sound', 'blink') NOT NULL DEFAULT 'popup' COMMENT 'Qual será o tipo de alerta?' ,
  `alert_message` TEXT NOT NULL COMMENT 'JSON Object contendo a mensagem do alerta e campos extras. Ex.: Se escolher email, haverá uma chave contendo todos os emails.' ,
  `block_other_jobs` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'O alerta bloqueará outros jobs?' ,
  `block_except` TEXT NULL COMMENT 'Caso o alerta bloqueie outros jobs, quais ele não irá bloquear?' ,
  PRIMARY KEY (`alert_id`) ,
  INDEX `fk_alerts_jobs_idx` (`job_id` ASC) ,
  CONSTRAINT `fk_alerts_jobs`
    FOREIGN KEY (`job_id` )
    REFERENCES `zeus`.`jobs` (`job_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zeus`.`runs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`runs` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`runs` (
  `run_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `job_id` INT(11) NOT NULL ,
  `run_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `run_start` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `run_end` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `run_http_code` VARCHAR(255) NOT NULL DEFAULT 0 ,
  `run_total_time` VARCHAR(255) NOT NULL DEFAULT 0 ,
  `run_redirect_count` VARCHAR(255) NOT NULL DEFAULT 0 ,
  `run_content_type` VARCHAR(255) NOT NULL DEFAULT 0 ,
  `run_others` TEXT NOT NULL COMMENT 'Conteúdo extra' ,
  `run_response` TEXT NOT NULL ,
  `run_comments` TEXT NULL COMMENT 'Comentários sobre a interação' ,
  `run_problems` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`run_id`) ,
  INDEX `fk_runnings_jobs1_idx` (`job_id` ASC) ,
  CONSTRAINT `fk_runnings_jobs1`
    FOREIGN KEY (`job_id` )
    REFERENCES `zeus`.`jobs` (`job_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `zeus`.`blocks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `zeus`.`blocks` ;

CREATE  TABLE IF NOT EXISTS `zeus`.`blocks` (
  `block_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `job_id` INT(11) NOT NULL ,
  `alert_id` INT(11) NOT NULL ,
  `block_status` TINYINT(1) NOT NULL DEFAULT 0 ,
  `block_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`block_id`) ,
  INDEX `fk_blocks_jobs1_idx` (`job_id` ASC) ,
  INDEX `fk_blocks_alerts1_idx` (`alert_id` ASC) ,
  CONSTRAINT `fk_blocks_jobs1`
    FOREIGN KEY (`job_id` )
    REFERENCES `zeus`.`jobs` (`job_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_blocks_alerts1`
    FOREIGN KEY (`alert_id` )
    REFERENCES `zeus`.`alerts` (`alert_id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `zeus` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
