# European competitions — season JSON

Import: `php artisan euro:import database/data/euro/<file>.json [--force]`

`--force` replaces an existing season (its ties and matches are deleted and re-created).
Nothing is written if any club is unknown — the command lists them.

Club reference = club `slug` (preferred), exact `name`, or a historical name from `club_names`.

```jsonc
{
  "competition": { "name": "European Champion Clubs' Cup", "slug": "european-cup" },
  "season": "1955-56",          // same format as domestic seasons
  "competition_name": "European Cup", // optional: historical name in this season
  "points_for_win": 2,          // for group tables of this season (default 3)
  "stages": [
    {
      "stage": "round_1",       // App\Enums\EuroStage value
      "ties": [
        {
          "clubs": ["sporting-cp", "fk-partizan"],   // first = listed first in the pairing
          "winner": "fk-partizan",                   // optional when aggregate decides; null = nobody advanced
          "note": null,                              // "won on coin toss", "pen. 4:3", ...
          "matches": [                               // in leg order; 3rd (2nd in a final) = play-off / replay, not in Agg.
            { "date": "1955-09-04", "home": "sporting-cp", "away": "fk-partizan", "score": "3:3" },
            { "date": "1955-10-12", "home": "fk-partizan", "away": "sporting-cp", "score": "5:2" }
          ]
        }
      ]
    },
    {
      "stage": "group",         // group stages: plain matches with "group"
      "matches": [
        { "group": "A", "date": "1991-11-27", "home": "...", "away": "...", "score": "1:0" }
      ]
    },
    {
      "stage": "final",
      "ties": [
        {
          "clubs": ["real-madrid-cf", "stade-de-reims"],
          "matches": [
            { "date": "1956-06-13", "home": "real-madrid-cf", "away": "stade-de-reims",
              "score": "4:3", "neutral": true, "extra_time": false, "note": "Parc des Princes, Paris" }
          ]
        }
      ]
    }
  ]
}
```

Match fields: `home`, `away`, `score` (`"4:3"`, after extra time; penalties go to `note`),
optional `date`, `extra_time`, `neutral`, `note`.

Tie winner: explicit `winner`, else the better aggregate of legs 1–2, else the play-off winner.

Season winner / runner-up are taken from the final tie automatically.
