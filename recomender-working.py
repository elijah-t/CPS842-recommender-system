import csv
import itertools
import math
import sys

# movies[movieID] = {movieID: movie_name}
movies = {}

# ratings[userID] = {movieID:rating}
ratings = {}

similarities = {}
usrList = []

current_user = 400

givenMovie = int(sys.argv[1])

# with open('movieIDs.csv', 'r') as movieIDs:
with open('movieIDs.csv', 'r') as movieIDs:
    mIDs = csv.reader(movieIDs)

    for row in mIDs:
        if (row[0] != "id"):
            movies[int(row[0])] = row[1]

# with open('ratings.csv', 'r') as movieRatings:
with open('ratings.csv', 'r') as movieRatings:
    mRatings = csv.reader(movieRatings)

    for row in mRatings:
        if (row[0] != "userId"):
            if (int(row[1]) in movies):
                if (int(row[0]) not in ratings):
                    ratings[int(row[0])] = {int(row[1]): float(row[2])}
                else:
                    ratings[int(row[0])][int(row[1])] = float(row[2])

for usr in ratings:
    usrList.append(usr)

#finds all unique pairs for similarities
def findPairs(usrList):
    pairs = []
    for pair in itertools.combinations(usrList, 2):
        # print(pair)
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
    
# def findUnratedMovie(pair):
#     unrated = 0
#     for movie in ratings[pair[0]]:
#         if movie not in ratings[pair[1]]:
#             unrated = movie
#     return unrated

def weightedSum():
    denom = 0
    numerator = 0

    for pair in findPairs(usrList):

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
                

            
    return(numerator/denom)

print(weightedSum())



#  ---------------------------------------------------------------------------
def test():

    output = open('fixed.csv', 'w')
    writer = csv.writer(output)


    with open('r.csv', 'r') as movieRatings:
        mRatings = movieRatings.readlines()
        for row in mRatings:
            newRow = row[:-1].split(",")
            if (newRow[1] != "movieId"):
                if int(newRow[1]) in movies:
                    writer.writerow([row[:-1]])


def fixFixed():
    with open('fixed.csv', 'r') as f:
        fix = f.readlines()

    for line in fix:
        line = line.replace('"',"")
        line = line.replace("\n", "")
        print(line)

                

# test()
# fixFixed()
# print(movies)