# storage/app/scripts/sentiment_analyzer.py

import sys
import json
from transformers import pipeline

def analyze_sentiments_hf(comments):
    # Carrega o pipeline de análise de sentimento com o modelo que você escolheu.
    # Na primeira vez que rodar, ele vai baixar o modelo (pode demorar um pouco).
    sentiment_analyzer = pipeline('sentiment-analysis', model='pysentimiento/bertweet-pt-sentiment')

    # Extrai apenas os textos dos comentários para analisar em lote (é mais rápido)
    texts = [comment.get('content', '') for comment in comments]
    
    # Roda a análise em todos os textos de uma vez
    analysis = sentiment_analyzer(texts)

    results = []
    for i, comment in enumerate(comments):
        model_result = analysis[i]
        label = model_result['label']
        score = model_result['score']
        
        # Converte o resultado ('POS', 'NEG', 'NEU') para o nosso score de -1 a 1
        final_score = 0.0
        if label == 'POS':
            final_score = score  # ex: 0.98
        elif label == 'NEG':
            final_score = -score # ex: -0.99
        # Se for 'NEU' (Neutro), o score já é 0.0
        
        results.append({
            "id": comment.get('id'),
            "score": final_score
        })
        
    return results

if __name__ == "__main__":
    try:
        comments_json = sys.argv[1]
        comments = json.loads(comments_json)
        
        analysis_results = analyze_sentiments_hf(comments)
        
        print(json.dumps(analysis_results))

    except Exception as e:
        error_message = {"error": str(e)}
        print(json.dumps(error_message))