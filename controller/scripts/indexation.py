#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import sys
import json
import mysql.connector
from mysql.connector import Error
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# Configuration de la base de données - À ADAPTER
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",  # Mettre votre mot de passe si nécessaire
    "database": "carrental",
    "charset": "utf8mb4"
}

def debug_log(message):
    """Journalisation des messages de debug"""
    print(f"DEBUG: {message}", file=sys.stderr)

def get_vehicles():
    """Récupère tous les véhicules disponibles avec vérification complète"""
    conn = None
    try:
        debug_log("Tentative de connexion à MySQL...")
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        
        # Requête de test plus simple pour debug
        debug_log("Exécution de la requête SQL...")
        cursor.execute("SELECT * FROM cars LIMIT 5")  # Test sans condition WHERE
        
        vehicles = cursor.fetchall()
        debug_log(f"Nombre de véhicules bruts trouvés: {len(vehicles)}")
        
        # Affiche le premier véhicule pour vérification
        if vehicles:
            debug_log(f"Exemple de véhicule: {vehicles[0]}")
        
        return vehicles
        
    except Error as e:
        debug_log(f"ERREUR MySQL: {str(e)}")
        return []
    finally:
        if conn and conn.is_connected():
            cursor.close()
            conn.close()

def main():
    try:
        # Lecture de la requête
        query = sys.stdin.read().strip()
        debug_log(f"Requête reçue: '{query}'")
        
        # Récupération des véhicules
        vehicles = get_vehicles()
        
        if not vehicles:
            debug_log("Aucun véhicule trouvé - vérifiez votre base de données")
            print(json.dumps({
                "statut": "success",
                "vehicules": [],
                "message": "Aucun véhicule trouvé dans la base de données"
            }))
            return
        
        # Test manuel avec des données fictives si la base échoue
        debug_log("Utilisation des véhicules de la base de données")
        
        # Vectorisation simple
        corpus = [f"{v['marque']} {v['model']}".lower() for v in vehicles]
        debug_log(f"Corpus exemple: {corpus[0] if corpus else 'vide'}")
        
        vectorizer = TfidfVectorizer(lowercase=True)
        tfidf = vectorizer.fit_transform(corpus)
        query_vec = vectorizer.transform([query.lower()])
        
        similarities = cosine_similarity(query_vec, tfidf).flatten()
        debug_log(f"Scores de similarité: {similarities}")
        
        # Préparation des résultats avec seuil très bas pour debug
        results = []
        for i, score in enumerate(similarities):
            vehicle = vehicles[i]
            debug_log(f"Véhicule: {vehicle['marque']} {vehicle['model']} - Score: {score}")
            if score > 0:  # Accepter tous les résultats pour debug
                results.append({
                    "id": vehicle["id"],
                    "marque": vehicle["marque"],
                    "model": vehicle["model"],
                    "score": float(score)
                })
        
        print(json.dumps({
            "statut": "success",
            "vehicules": results,
            "debug_info": {
                "query": query,
                "vehicles_count": len(vehicles),
                "scores": [float(s) for s in similarities]
            }
        }))
        
    except Exception as e:
        debug_log(f"ERREUR: {str(e)}")
        print(json.dumps({
            "statut": "error",
            "message": str(e)
        }))
        sys.exit(1)

if __name__ == "__main__":
    main()