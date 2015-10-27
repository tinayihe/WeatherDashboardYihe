-- Database: "weatherDashboard"

-- DROP DATABASE "weatherDashboard";

CREATE DATABASE "weatherDashboard"
  WITH OWNER = postgres
       ENCODING = 'UTF8'
       TABLESPACE = pg_default
       LC_COLLATE = 'French_France.1252'
       LC_CTYPE = 'French_France.1252'
       CONNECTION LIMIT = -1;

-- Table: cities

-- DROP TABLE cities;

CREATE TABLE cities
(
  id serial NOT NULL,
  city_name text NOT NULL,
  city_id text NOT NULL,
  CONSTRAINT pk_id PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE cities
  OWNER TO postgres;
