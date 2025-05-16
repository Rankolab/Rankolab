
import React, { useEffect } from 'react';
import { initAdsense } from '../../lib/adsense';

interface AdComponentProps {
  adSlot: string;
  adFormat?: string;
  style?: React.CSSProperties;
}

export default function AdComponent({ adSlot, adFormat = 'auto', style }: AdComponentProps) {
  useEffect(() => {
    initAdsense();
  }, []);

  return (
    <ins
      className="adsbygoogle"
      style={style || { display: 'block' }}
      data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"
      data-ad-slot={adSlot}
      data-ad-format={adFormat}
      data-full-width-responsive="true"
    />
  );
}
