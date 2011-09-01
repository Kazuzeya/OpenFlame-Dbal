# Changelog

## Changes since 1.0

* [Change] All DBMS-specific items will now be handled in the child class (which is instanced when calling Query::newInstance())
* [Change] PDO will throw exceptions by default
* [Removal] Got rid of Utilities for version 1.1
