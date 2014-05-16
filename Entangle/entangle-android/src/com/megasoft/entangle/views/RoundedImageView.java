package com.megasoft.entangle.views;

import com.squareup.picasso.Picasso.LoadedFrom;
import com.squareup.picasso.Target;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Bitmap.Config;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.PorterDuff.Mode;
import android.graphics.PorterDuffXfermode;
import android.graphics.Rect;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.widget.ImageView;


public class RoundedImageView extends ImageView implements Target {
	
	private Bitmap bitmap;

	public RoundedImageView(Context context) {
	    super(context);
	}
	
	public RoundedImageView(Context context, AttributeSet attrs) {
	    super(context, attrs);
	}
	
	public RoundedImageView(Context context, AttributeSet attrs, int defStyle) {
	    super(context, attrs, defStyle);
	}
	
	@Override
	protected void onDraw(Canvas canvas) {
	
	    Drawable drawable = getDrawable();
	
	    if (drawable == null) {
	        return;
	    }
	
	    if (getWidth() == 0 || getHeight() == 0) {
	        return; 
	    }
	    if( bitmap == null){
	    	bitmap =  ((BitmapDrawable)drawable).getBitmap() ;
	    }
	    Bitmap bitmapCopy = bitmap.copy(Bitmap.Config.ARGB_8888, true);
	
	    int width = getWidth(), height = getHeight();
	
	
	    Bitmap roundBitmap =  getCroppedBitmap(bitmapCopy, width);
	    canvas.drawBitmap(roundBitmap, 0,0, null);
	
	}
	
	public static Bitmap getCroppedBitmap(Bitmap bmp, int radius) {
	    Bitmap sbmp;
	    if(bmp.getWidth() != radius || bmp.getHeight() != radius)
	        sbmp = Bitmap.createScaledBitmap(bmp, radius, radius, false);
	    else
	        sbmp = bmp;
	    Bitmap output = Bitmap.createBitmap(sbmp.getWidth(),
	            sbmp.getHeight(), Config.ARGB_8888);
	    Canvas canvas = new Canvas(output);
	
	    final int color = 0xffa19774;
	    final Paint paint = new Paint();
	    final Rect rect = new Rect(0, 0, sbmp.getWidth(), sbmp.getHeight());
	
	    paint.setAntiAlias(true);
	    paint.setFilterBitmap(true);
	    paint.setDither(true);
	    canvas.drawARGB(0, 0, 0, 0);
	    paint.setColor(Color.parseColor("#BAB399"));
	    canvas.drawCircle(sbmp.getWidth() / 2+0.7f, sbmp.getHeight() / 2+0.7f,
	            sbmp.getWidth() / 2+0.1f, paint);
	    paint.setXfermode(new PorterDuffXfermode(Mode.SRC_IN));
	    canvas.drawBitmap(sbmp, rect, rect, paint);
	
	
	            return output;
	}
	
	@Override
	public void onBitmapFailed(Drawable arg0) {}
	
	@Override
	public void onBitmapLoaded(Bitmap arg0, LoadedFrom arg1) {
		bitmap = arg0;
	}
	
	@Override
	public void onPrepareLoad(Drawable arg0) {}

}