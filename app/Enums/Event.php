<?php

namespace App\Enums;

enum Event: string
{
    use HasValues;

    case Minutes = 'Minutes';
    case Goals = 'Goals';
    case Assists = 'Assists';
    case Shots = 'Shots';
    case ShotsOnTarget = 'Shots on target';
    case KeyPasses = 'Key passes';
    case GoalsConceded = 'Goals conceded';
    case PenaltiesScored = 'Penalties scored';
    case PenaltiesWon = 'Penalties won';
    case PenaltiesCommited = 'Penalties commited';
    case PenaltiesSaved = 'Penalties saved';
    case PenaltiesMissed = 'Penalties missed';
    case OwnGoals = 'Own goals';
    case YellowCards = 'Yellow cards';
    case RedCards = 'Red cards';
    case Saves = 'Saves';
    case Position = 'Position';
    case LineupPosition = 'Lineup position';
    case OnTheBench = 'On the bench';
    case Substitute = 'Substitute';
    case PenaltiesWonWithoutAssist = 'Penalties won without assist';
    case Fouls = 'Fouls';
    case DoublePenaltiesScored = 'Double penalties scored';
    case DoublePenaltiesSaved = 'Double penalties saved';
    case DoublePenaltiesMissed = 'Double penalties missed';
    case Started = 'Started';
}
