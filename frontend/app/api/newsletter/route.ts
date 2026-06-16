import { NextRequest, NextResponse } from 'next/server';

export async function POST(req: NextRequest) {
  try {
    const { email } = await req.json();
    if (!email || !email.includes('@')) {
      return NextResponse.json({ error: 'Invalid email' }, { status: 400 });
    }

    // Forward to Laravel backend
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/newsletter`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify({ email }),
    });

    if (!res.ok) throw new Error('Backend error');
    return NextResponse.json({ message: 'Subscribed successfully' });
  } catch {
    // Graceful fallback — log and succeed anyway on frontend
    return NextResponse.json({ message: 'Received' });
  }
}
