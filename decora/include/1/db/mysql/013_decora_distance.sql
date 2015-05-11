

DROP FUNCTION IF EXISTS decora_distance;

CREATE FUNCTION decora_distance(p1 Double precision,p2 Double precision,p3 Double precision,p4 Double precision) RETURNS Double precision
BEGIN
    RETURN sqrt(
                pow( (p2-p4) * cos(p1 * pi() / 180),2)
                +
                pow(p3-p1,2)
            ) * pi() * 12756.274 / 360 ;

END

