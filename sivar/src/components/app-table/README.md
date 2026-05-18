it is advisable to use this table only with a list of records with a format like: {[key:string]: VALUE}[](consider this like a WILDCARD[]).
where typeOf(VALUE) is a basicType(bool,string, number) y not a complexType(Array, Object).

however the table can handle 3 special cases:

1. VALUE is a list of values with basicType. in this case the values will be display as a list inside of the table cell
2. VALUE is an nested object. if this case you can use Column.path to specify the access to the required value.
   consider the fallowing object NestedObject = {father4: {father3: {father2: {father: {child: VALUE}}}}}.
   - if father is an complexType(object)to access to VALUE, Column.Path must be Column.path=['father4', 'father3', 'father2','father', 'child']. note that the values are ordered to access from the outside[index=0] inwards[index=last]. additionally for this case VALUE could be like case 1.
   - if father is a complexTpe(Array) father=[{chaild: VALUE1}, {chaild: VALUE2}, {chaild: VALUE3}] you must specify Column.path and set the Column.isLastFatherList=True. this will extract all VALUES from its children. this case don't allow that VALUE could be like case 1.
     note: if father2, father3, father4... have complexType(array) this only will extract the first object from each parent
3. You can add virtual Columns. consider a virtual Column a column/field that is not in the record.
   - in this case yo can specify Column.keyName to get a column which be a composition of several values.
     example:
     Consider
     OBJECT_TEST_LIKE = {var1, VALUE1, var2: VALUE2, var3: VALUE3}
     COLUMN_JOIN = {keyName:'join', keyNames:['var1','var3'], text: 'column join'}
     this will show a table with a column with name 'column join' and with the list of values [VALUE1,VALUE3]
     NOTE: this case is not compatible with case 2. if you specify Column.path and Column.keyNames it will raise an error.
