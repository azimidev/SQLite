-- COUNT

SELECT COUNT(*) FROM City;

SELECT District, COUNT(*) FROM City GROUP BY District;

SELECT District, COUNT(*) AS Count FROM City
    GROUP BY District
    HAVING Count > 10
    ORDER BY Count DESC;

-- SUM/TOTAL
SELECT SUM(Population) FROM Country;
SELECT TOTAL(Population) FROM Country;

SELECT Continent, SUM(Population) AS Pop FROM Country
    GROUP BY Continent
    ORDER BY Pop DESC;

-- MIN/MAX

SELECT MAX(SurfaceArea) FROM Country;
SELECT Continent, MAX(SurfaceArea) FROM Country GROUP BY Continent;

SELECT c.Name as Country, csa.Continent, csa.MaxSA FROM 
  ( SELECT Continent, MAX(SurfaceArea) as MaxSA FROM Country GROUP BY Continent ) AS csa
  JOIN Country AS c
    ON c.SurfaceArea = csa.MaxSA
  ORDER BY MaxSA DESC;

-- AVG

SELECT AVG(Population) FROM City;

SELECT District, AVG(Population) AS AvgPop FROM City
    GROUP BY District
    HAVING District != '' AND AvgPop > 1000000
    ORDER BY AvgPop DESC;

-- GROUP BY
SELECT COUNT(*) FROM City;
SELECT COUNT(*) FROM City GROUP BY District;

SELECT District, COUNT(*) AS Count FROM
    City GROUP BY District
    ORDER BY Count DESC;

-- HAVING
SELECT District, AVG(Population) AS AvgPop FROM City
    GROUP BY District;

SELECT District, AVG(Population) AS AvgPop FROM City
    GROUP BY District
    HAVING AvgPop > 1000000;


