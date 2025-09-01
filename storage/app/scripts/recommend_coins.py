import sys
import json

def get_ideal_portfolio(profile):
    """Retorna a distribuição de risco ideal para um dado perfil de investidor."""
    if profile == 'conservative':
        return {'low': 0.70, 'medium': 0.20, 'high': 0.10}
    elif profile == 'moderate':
        return {'low': 0.40, 'medium': 0.40, 'high': 0.20}
    elif profile == 'aggressive':
        return {'low': 0.10, 'medium': 0.40, 'high': 0.50}
    else:
        return {'low': 0.0, 'medium': 0.0, 'high': 0.0}

def calculate_current_portfolio(user_balances):
    """Calcula a distribuição de risco percentual da carteira atual do usuário."""
    risk_values = {'low': 0.0, 'medium': 0.0, 'high': 0.0}
    total_portfolio_value = 0.0

    for balance in user_balances:
        value = float(balance['balance']) * float(balance['price'])
        risk_category = balance['risk']
        
        if risk_category in risk_values:
            risk_values[risk_category] += value
        
        total_portfolio_value += value

    if total_portfolio_value == 0:
        return {'low': 0.0, 'medium': 0.0, 'high': 0.0}

    risk_percentages = {
        'low': risk_values['low'] / total_portfolio_value,
        'medium': risk_values['medium'] / total_portfolio_value,
        'high': risk_values['high'] / total_portfolio_value
    }
    return risk_percentages

def generate_recommendations(ideal, current, user_coin_ids, all_coins_info):
    """
    Gera uma lista ORDENADA de IDs de moedas para rebalancear a carteira,
    priorizando as categorias de risco mais defasadas.
    """
    
    gaps = []
    for category, ideal_percentage in ideal.items():
        current_percentage = current.get(category, 0.0)
        gap = ideal_percentage - current_percentage
        
        if gap > 0:
            gaps.append({'category': category, 'gap': gap})

    if not gaps:
        return []

    sorted_gaps = sorted(gaps, key=lambda x: x['gap'], reverse=True)
    
    underfunded_categories_ordered = [item['category'] for item in sorted_gaps]

    potential_recommendations = []
    for coin in all_coins_info:
        if coin['risk'] in underfunded_categories_ordered:
            potential_recommendations.append(coin)
            
    final_recommendations = sorted(potential_recommendations, key=lambda coin: underfunded_categories_ordered.index(coin['risk']))
    
    recommended_ids = [coin['id'] for coin in final_recommendations]
    
    return recommended_ids


if __name__ == "__main__":
    try:
        user_profile = sys.argv[1]
        user_balances_json = sys.argv[2]
        all_coins_json = sys.argv[3]

        user_balances = json.loads(user_balances_json)
        all_coins = json.loads(all_coins_json)

        ideal = get_ideal_portfolio(user_profile)
        current = calculate_current_portfolio(user_balances)
        
        user_coin_ids = [balance['coin_id'] for balance in user_balances]
        
        recommendations = generate_recommendations(ideal, current, user_coin_ids, all_coins)

        print(json.dumps(recommendations))
    except Exception as e:
        error_message = {"error": str(e), "args": sys.argv[1:]}
        print(json.dumps(error_message))