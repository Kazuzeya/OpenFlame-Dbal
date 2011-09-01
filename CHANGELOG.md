# Changelog

## Changes since 1.0

* [Feature] Give an option to have fetchRowset index its result by column within the result set.
* [Change] All DBMS-specific items will now be handled in the child class (which is instanced when calling Query::newInstance())
* [Change] Query is no longer being inherited by QueryBuilder
* [Change] PDO will throw exceptions by default
* [Removal] Got rid of Utilities for version 1.1
