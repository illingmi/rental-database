Project Overview:

This is a rental database that was designed to stimulate a car rental system. We were required to define the data tables, 
write the code for the transactions and design an reasonable user interface. Before the data tables were implemented, the 
database was organized using an Entity Relationship diagram.

Data tables were created for:

- Reservations, rentals, vehicles, vehicleTypes, customers, returns

Transactions Implemented:

Transactions performed by a customer:

- View the number of available vehicles for a specific car type, location, and time interval. The user should be able to provide any
subset of {car type, location, time interval} to view the available vehicles. If the user provides no information, the application
automatically returns to a sorted list of all vehicles (at that branch). There is also an option for the user to see the details of the
available vehicles.
 
- Make a reservation. If a customer is new, the customer’s details are added to the database. Upon successful completion, a 
confirmation number for the reservation should be shown along with the details entered during the reservation. If the customer’s 
desired vehicle is not available, an appropriate error message should be shown. 


Transactions performed by a clerk: 
- Renting a Vehicle: The system will displays a receipt with the necessary details (e.g., confirmation number, date of reservation, 
type of car, location, how long the rental period lasts for, etc.) for the customer.  
 
- Returning a Vehicle: Only a rented vehicle can be returned. Trying to return a vehicle that has not been rented generates an error 
message for the clerk. When returning a vehicle, the system will display a receipt with the necessary details (e.g., reservation 
confirmation number, date of return, how the total was calculated etc.) for the customer. 

 
Generates a report for: 
- Daily Rentals: This report contains information on all the vehicles rented out during the day. The entries are grouped by branch, and 
within each branch, the entries are grouped by vehicle category. The report also displays the number of vehicles rented per category 
(e.g., 5 sedan rentals, 2 SUV rentals, etc.), the number of rentals at each branch, and the total number of new rentals across the 
whole company.
 
- Daily Rentals for Branch: This is the same as the Daily Rental report but it is for one specified branch 
 
- Daily Returns: The report contains information on all the vehicles returned during the day. The entries are grouped by branch, and within each branch, the entries are grouped by vehicle category. The report also shows the number of vehicles returned per category, the revenue per category, subtotals for the number of vehicles and revenue per branch, and the grand totals for the day. 
 
- Daily Returns for Branch: This is the same as the Daily Returns report, but it is for one specified branch.  
