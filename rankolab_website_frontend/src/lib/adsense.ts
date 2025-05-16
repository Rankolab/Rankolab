
export const initAdsense = () => {
  if (typeof window !== 'undefined') {
    (window.adsbygoogle = window.adsbygoogle || []).push({});
  }
};

export const ADS_CLIENT_ID = 'ca-pub-XXXXXXXXXXXXXXXX'; // Replace with actual AdSense client ID
