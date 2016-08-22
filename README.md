# SQL-function

## How To Use

### Preset
```
dbname : Defined in db.php
table : Defined in db.php
column : *
order : ASC
limit : No limit
```

### For All

```PHP
$query = new query;
$query->dbname = 'dbname';
$query->table = 'table';
```

### SELECT
```PHP
$query = new query;
$query->column = array('column');
$query->where = array(
	array('column','value','operator')
);
$query->order = array(
	array('column1','DESC'),
	array('column2')
);
$query->group = array('column');
$query->limit = array(0,10);
$row = $query->SELECT();
```
* Return value: Data
* Return type: Two-dimensional array

### SELECT one line
```PHP
...
$row = $query->SELECT(true);
```
* Return value: Data
* Return type: One-dimensional array

### INSERT
```PHP
$query = new query;
$query->value = array(
	array('column','value')
);
$query->INSERT();
```
* Return value: Is success
* Return type: Boolean

### UPDATE
```PHP
$query = new query;
$query->value = array(
	array('column','value')
);
$query->where = array(
	array('column','value','operator')
);
$query->$limit = array(0,10);
$query->UPDATE();
```
* Return value: Executed rows count
* Return type: Integer

### DELETE
```PHP
$query = new query;
$query->where = array(
	array('column','value','operator')
);
$query->$limit = array(0,10);
$query->DELETE();
```
* Return value: Executed rows count
* Return type: Integer

### SQL
```PHP
$query = new query;
$query->query = "SQL string";
$query->SQL();
```
* Return value: PDO::query()
* Return type: PDOStatement
