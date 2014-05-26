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
import android.util.Log;
import android.widget.ImageView;


public class RoundedImageView extends ImageView  {
	
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
	
}