import random
import string

cars = ['Ford', 'BMW', 'Audi', 'Toyota']
models = ['aa', 'bb', 'cc', 'dd']
types = ['economy', 'compact', 'standard', 'mid-size', 'full-size', 'SUV', 'truck']
color = ['black', 'red', 'white', 'blue']
location = ['Granville', 'Main', 'Davie']
city = 'Vancouver'
list_of_vlicense = []
list_of_vids = []
status = ['rented', 'not-rented']


for x in range(50):
    a = ''.join(random.choice(string.ascii_lowercase + string.digits) for _ in range(6))
    if(a not in list_of_vlicense):
        list_of_vlicense.append(a)

for y in range(50):
    b = 'v' + str(y)
    list_of_vids.append(b)

for z in range(20):
    year = random.randrange(2010,2019)
    odometer_level = random.randrange(1000)
    print('insert into Vehicle values( \'' + list_of_vids[z] + '\', \'' + list_of_vlicense[z] + '\', \'' + random.choice(cars) + '\', \'' +
    random.choice(models) + '\', ' + str(year) + ', \'' + random.choice(color) + '\', ' + str(odometer_level) + ', \''
    + random.choice(status) + '\', \'' + random.choice(types) + '\', \'' + random.choice(location) + '\', \'' + city + '\');')
