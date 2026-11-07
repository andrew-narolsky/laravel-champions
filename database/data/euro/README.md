# European competitions — season JSON

Files: one folder per competition, one file per season — `database/data/euro/<competition-slug>/<season>.json`
(e.g. `champions-league/1955-56.json`, `europa-league/1971-72.json`, `conference-league/2021-22.json`).

Import: `php artisan euro:import database/data/euro/champions-league/1955-56.json [--force]`

Logo: `database/data/images/euro/<competition-slug>.webp` — attached to the competition on import.
Hero banner (optional): `resources/assets/images/<competition-slug>-banner.webp`, otherwise the default banner.

All seasons of a competition: `for f in database/data/euro/champions-league/*.json; do php artisan euro:import "$f" --force; done`

`--force` replaces an existing season (its ties and matches are deleted and re-created).
Nothing is written if any club is unknown — the command lists them.

Club reference = club `slug` (preferred), exact `name`, or a historical name from `club_names`.

```jsonc
{
  "competition": { "name": "European Champion Clubs' Cup", "slug": "european-cup" },
  "season": "1955-56",          // same format as domestic seasons
  "competition_name": "European Cup", // optional: historical name in this season
  "points_for_win": 2,          // for group tables of this season (default 3)
  "point_adjustments": [        // optional: deductions in group tables, shown as a footnote
    { "club": "ac-milan", "stage": "group", "points": -2, "note": "Milan were docked two points ..." }
  ],
  "stages": [
    {
      "stage": "round_1",       // App\Enums\EuroStage value
      "ties": [
        {
          "clubs": ["sporting-cp", "fk-partizan"],   // first = listed first in the pairing
          "winner": "fk-partizan",                   // optional when aggregate decides; null = nobody advanced
          "note": null,                              // "won on coin toss", "pen. 4:3", ...
          "matches": [                               // in leg order; 3rd (2nd in a one-match final) = play-off / replay, not in Agg.
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

Final: one match at a neutral venue (`"neutral": true`), or two legs home and away (UEFA Cup until 1997):
a final whose first match is not neutral counts legs 1–2 in the aggregate.

Season winner / runner-up are taken from the final tie automatically.
