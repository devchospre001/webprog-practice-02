# SQL Reports for Orders and Employee Performance

This document explains two important SQL queries:

- **Order Report** – calculates total spent per order and formats it as an HTML table row.  
- **Employee Performance Report** – calculates total payments handled by each employee.

---

## 1. Order Report Query

```sql
    SELECT o.orderNumber AS order_number,
      SUM(od.quantityOrdered * od.priceEach) AS total_amount,
      CONCAT(
      '<tr><td>', o.orderNumber, '</td>',
      '<td>', SUM(od.quantityOrdered * od.priceEach), '</td></tr>'
      ) AS details
      FROM orders o
      JOIN orderdetails od ON o.orderNumber = od.orderNumber
      GROUP BY o.orderNumber
      ORDER BY o.orderNumber DESC;
```

**Query Purpose:**  
Generate a report per order, summing the total amount spent and preparing an HTML table row for each order.

**Query Explanation Line by Line:**  

- **order_number** – the unique ID of the order.  
- **total_amount** – the sum of `quantityOrdered × priceEach` for all items in the order.  
- **details** – an HTML table row string containing the order number, customer name, and total amount. This can be directly used in a webpage table.  
- **FROM orders o** – start from the `orders` table, aliased as `o`.  
- **JOIN orderdetails od ON o.orderNumber = od.orderNumber** – join `orderdetails` to get quantities and prices for each order.  
- **JOIN customers c ON o.customerNumber = c.customerNumber** – join `customers` to get customer names.  
- **GROUP BY o.orderNumber, c.customerName** – aggregate totals per order and customer.  
- **ORDER BY o.orderNumber** – sort the results by order number in ascending order.  

**Example Output Table:**

| order_number | total_amount | details                                                      |
|-------------|-------------|--------------------------------------------------------------|
| 101         | 250.50      | `<tr><td>101</td><td>Acme Corp</td><td>250.50</td></tr>`    |
| 102         | 120.00      | `<tr><td>102</td><td>Global Inc</td><td>120.00</td></tr>`   |

---

## 2. Employee Performance Report Query

```sql
      SELECT e.employeeNumber AS id,
      CONCAT(e.firstName, ' ', e.lastName) as full_name, 
      e.email,
      COALESCE(SUM(p.amount), 0) AS total
      FROM employees e
      LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber
      LEFT JOIN payments p ON c.customerNumber = p.customerNumber
      GROUP BY e.employeeNumber, e.firstName, e.lastName, e.email
      ORDER BY total DESC
```

**Query Purpose:**  
Generate a performance report for every employee, summing all payments made by their customers.

**Query Explanation Line by Line:**  

- **id** – employee's unique ID.  
- **full_name** – concatenation of first and last name.  
- **email** – employee's email address.  
- **total** – sum of all payments handled by the employee's customers.  
- **COALESCE(SUM(p.amount), 0)** – ensures employees with no payments appear as 0.  
- **FROM employees e** – start from the `employees` table, aliased as `e`.  
- **LEFT JOIN customers c ON e.employeeNumber = c.salesRepEmployeeNumber** – link all customers assigned to the employee.  
- **LEFT JOIN payments p ON c.customerNumber = p.customerNumber** – sum payments from each customer.  
- **GROUP BY e.employeeNumber, e.firstName, e.lastName, e.email** – aggregate per employee.  
- **ORDER BY total DESC** – sort by total payments handled, highest first.  

**Example Output Table:**

| id  | full_name    | email               | total  |
|-----|-------------|-------------------|--------|
| 100 | John Doe    | john@example.com   | 5000.0 |
| 101 | Jane Smith  | jane@example.com   | 3200.0 |
| 102 | Bob Johnson | bob@example.com    | 0.0    |

---

## Summary

- The **Order Report** shows how much each customer spent per order and produces HTML rows for display.  
- The **Employee Performance Report** identifies top-performing employees based on payments collected.  
- Aggregation using `SUM` and joining related tables ensures accurate totals.  
- Use `COALESCE` and `LEFT JOIN` to include employees or orders even if no payments exist.  
