# PermissionTest

This script checks if all your apex classes is accessible to the chosen permission set. It returns "Success"
or error message.

How it works:

* Download file "permissionstest.php"
* Make it executable "chmod u+x ./permissionstest.php"
* Create file "setup.json" looks like this:
```
{
  "file": "/Users/someUser/IdeaProjects/MyOrg1/src/permissionsets/DaDaDocs_Set.permissionset",
  "path": "/Users/someUser/IdeaProjects/MyOrg1/src"
}
```
where "file" - path to permission set file and "path" - path to src salesforce folder
* run "./permissionstest.php"