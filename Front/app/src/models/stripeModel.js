import { loadStripe } from '@stripe/stripe-js';

const stripePubKey = import.meta.env.VITE_API_STRIPE_PUB;
const stripePromise = loadStripe(stripePubKey);

export async function createCheckoutSession(userId) {
  try {
    const response = await fetch(`http://127.0.0.1:9999/stripe/checkout-session/${userId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    const data = await response.json();

    if (!data.sessionId) {
      throw new Error('Session Stripe invalide.');
    }

    return data.sessionId;

  } catch (err) {
    console.error('Erreur lors de la création de la session Stripe :', err);
    throw err;
  }
}

export async function redirectToStripeCheckout(userId) {
  const stripe = await stripePromise;
  const sessionId = await createCheckoutSession(userId);
  await stripe.redirectToCheckout({ sessionId });
}
