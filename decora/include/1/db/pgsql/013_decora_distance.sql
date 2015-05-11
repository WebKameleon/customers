CREATE OR REPLACE FUNCTION decora_distance(Double precision,Double precision,Double precision,Double precision) RETURNS Double precision
AS $$
    SELECT sqrt(
                pow( ($2-$4) * cos($1 * pi() / 180),2)
                +
                pow($3-$1,2)
            ) * pi() * 12756.274 / 360

$$ LANGUAGE 'sql';

