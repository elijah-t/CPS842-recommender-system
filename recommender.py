import csv
import itertools
import math
import sys
import mysql.connector
import random

mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  password="",
  database="recommender"
)

getRatings = mydb.cursor()
getRatings.execute("SELECT * FROM ratings")
ratingsTable = getRatings.fetchall()

getMovies = mydb.cursor()
getMovies.execute("SELECT * FROM movie")
moviesTable = getMovies.fetchall()

# movies[movieID] = {movieID: movie_name}
movies = {}

# ratings[userID] = {movieID:rating}
ratings = {}

similarities = {}
usrList = []


# User input => python recommender.py userID movieID top5_Flag
current_user = int(sys.argv[1])
givenMovie = int(sys.argv[2])
wantTop5 = int(sys.argv[3])

# current_user = 15
# givenMovie = 3000


for row in moviesTable:
    if (row[0] != "id"):
        movies[int(row[0])] = row[1]

for row in ratingsTable:
    if (row[0] != "userId"):
        if (int(row[1]) in movies):
            if (int(row[0]) not in ratings):
                ratings[int(row[0])] = {int(row[1]): float(row[2])}
            else:
                ratings[int(row[0])][int(row[1])] = float(row[2])

for usr in ratings:
    usrList.append(usr)

# ------------------------------------------------




# ------------------------------------------------


#finds all unique pairs for similarities
def findPairs(usrList):
    pairs = []
    for pair in itertools.combinations(usrList, 2):
        if ((current_user in pair) and (givenMovie in ratings[pair[0]] or givenMovie in ratings[pair[1]]) and (givenMovie not in ratings[current_user])): #makes sure we only look at similarities of other user with current user
        # if (current_user in pair):
            pairs.append(pair)
    return pairs

def getAvgs(pair):
    sumA = 0
    sumB = 0
    count = 0

    for movieID in movies:
        if (movieID in ratings[pair[0]] and movieID in ratings[pair[1]]):
            count += 1
            sumA += ratings[pair[0]][movieID]
            sumB += ratings[pair[1]][movieID]

    return((sumA/count),(sumB/count))

def getSimilarity(pair):
    avgA, avgB = getAvgs(pair)
    numerator = 0
    sumA = 0
    sumB = 0

    try:
        for movieID in movies:
            
            if (movieID in ratings[pair[0]] and movieID in ratings[pair[1]]):
                # print(movieID)
                if (pair[0] == current_user):
                    product = (ratings[pair[1]][movieID]-avgA)*(ratings[pair[0]][movieID]-avgB)
                    numerator = numerator+product
                    sumA = sumA + (ratings[pair[1]][movieID]-avgA)**2
                    sumB = sumB + (ratings[pair[0]][movieID]-avgB)**2
                else:
                    product = (ratings[pair[0]][movieID]-avgA)*(ratings[pair[1]][movieID]-avgB)
                    numerator = numerator+product
                    sumA = sumA + (ratings[pair[0]][movieID]-avgA)**2
                    sumB = sumB + (ratings[pair[1]][movieID]-avgB)**2
        
        denom = math.sqrt(sumA*sumB)
        
        return(numerator/denom) #This is the similarity of the given pair

    except:
        return(-1)
    
def weightedSum():
    denom = 0
    numerator = 0

    for pair in findPairs(usrList):
        # print(getSimilarity(pair))
        try:            
            if (getSimilarity(pair) > 0):
                if (pair[0] == current_user):
                    numerator += (getSimilarity(pair) * ratings[pair[1]][givenMovie])
                    denom += getSimilarity(pair)
                else:
                    numerator += (getSimilarity(pair) * ratings[pair[0]][givenMovie])
                    denom += getSimilarity(pair)
        except:
            numerator += 0
            denom += 0
                

    if (denom == 0):
            return ("Not enough users have rated this movie")        
    return(numerator/denom)

if wantTop5 == 0:
    if isinstance(weightedSum(), float): 
        print("{:.2f}".format(weightedSum()))
    else:
        print(weightedSum())
else:
    finalReturn = ""

    
    # else:
    #     finalReturn += (weightedSum()+",")


    recomendedMovies = {}
    for movie in movies:
        if movie not in ratings[current_user]:
        # rating = weightedSum()
            givenMovie = movie
            if isinstance(weightedSum(), float):
                recomendedMovies[movie] = weightedSum()

    top5 = sorted(recomendedMovies, key=recomendedMovies.get, reverse=True)[:5]

    for topMovie in top5:
        finalReturn += (str(topMovie) + " " + str("{:.2f}".format(recomendedMovies[topMovie])) + ",")

    print(finalReturn)


        