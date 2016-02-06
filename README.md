# SQL-function

## How To Use

### Preset
```
dbname : Defined in db.php
column : *
order : ASC
limit : No limit
```

### SELECT

```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
$query->column = array('column');
$query->where = array(
	array('column','value','operator')
);
$query->order = array(
	array('column1','DESC'),
	array('column2')
);
$query->group = array('column');
$query->$limit = array(0,10);
$row = SELECT($query);
```

### SELECT one line
```PHP
...
$row = fetchone(SELECT($query));
```

### INSERT
```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
$query->value = array(
	array('column','value')
);
INSERT($query);
```

### UPDATE
```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
$query->value = array(
	array('column','value')
);
$query->where = array(
	array('column','value','operator')
);
$query->$limit = array(0,10);
UPDATE($query);
```

### DELETE
```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
$query->where = array(
	array('column','value','operator')
);
$query->$limit = array(0,10);
DELETE($query);
```

### SQL
```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
$query->query = "SQL string";
SQL($query);
```
